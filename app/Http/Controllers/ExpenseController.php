<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Expense;
use App\Models\HistoryBalance;
use App\Models\MapData;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    protected $_create = 'EXCR';
    protected $_update = 'EXUP';
    protected $_readall = 'EXRA';
    protected $_readid = 'EXRD';

    public function index($year = null)
    {
        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $currYear = date('Y');
        if ($year) {
            $plainYear = SecureHelper::unsecure($year);

            if ($plainYear) {
                $currYear = $plainYear;
            }
        }

        $years = $this->getYears();
        $division = $this->getDivisions();

        $view = ['divisionArr' =>  $division, 'yearArr' =>  $years, 'year' => $currYear, 'is_create' => $this->hasPrivilege($this->_create)];

        return view('contents.expense.index', $view);
    }

    public function add($type)
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        if (!in_array($type, config('global.type.code'))) {
            return abort(404);
        }

        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $typeDesc = $types[$type];
        $years = $this->getYears();
        $divisions = $this->getDivisions();
        $expense_id = $this->generate();

        $is_red = false;
        if ($type == config('global.type.code.red')) {
            $is_red = true;
        }

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'type' => $type, 'expense_id' => $expense_id, 'action' => route('transaction.expense.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'is_red' => $is_red, 'typeDesc' => $typeDesc, 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.expense.add', $view);
    }

    public function edit($id)
    {
        if (!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Expense::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $ma = Data::find($data['data_id'])->toArray();
        $year = $ma['year'];
        $division_id = $ma['division_id'];
        $ma_id = $ma['ma_id'];
        $map = MapData::where('data_id', $data['data_id'])->get()->toArray();
        $staffArr = array();
        foreach ($map as $value) {
            $data[] = array(
                'id' => $value['staff_id'],
                'text' => $value['staff'],
            );
        }

        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $typeDesc = $types[$data['type']];
        $years = $this->getYears();
        $divisions = $this->getDivisions();

        $is_red = false;
        if ($data['type'] == config('global.type.code.red')) {
            $is_red = true;
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('expense_id' => $data['expense_id']);
        }

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'staffArr' => $staffArr, 'action' => route('transaction.expense.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'is_red' => $is_red, 'typeDesc' => $typeDesc, 'year' => $year, 'division_id' => $division_id, 'ma_id' => $ma_id, 'mandatory' => $this->hasPrivilege($this->_update)];

        return view('contents.expense.edit', array_merge($view, $data));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $param = $request->input('id');

            if (!isset($param)) {
                $year = 0;
                $division = 0;
            } else {
                $param = SecureHelper::unpack($param);

                if (!is_array($param)) {
                    $year = 0;
                    $division = 0;
                } else {
                    $year = $param['year'];
                    $division = $param['id'];
                }
            }

            $data = Data::select(['id'])->where('is_trash', 0)->where('year', $year)->where('division_id', $division)->orderBy('id')->get()->toArray();
            $data = array_column($data, 'id');
            $expense = Expense::select(['id', 'expense_id', 'expense_date', 'reff_no', 'reff_date', 'staff_id', 'amount', 'type', 'updated_at'])->whereIn('data_id', $data)->orderBY('updated_at', 'desc');
            $table = DataTables::eloquent($expense);
            $rawColumns = array('expense', 'status_desc');
            $table->addIndexColumn();

            $table->addColumn('expense', function ($row) {
                if ($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="' . route('transaction.expense.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->expense_id . '</a>';
                } else {
                    $column = $row->expense_id;
                }

                return $column;
            });

            $table->addColumn('status_desc', function ($row) {
                $column = '';
                if ($row->status == config('global.type.status.white')) {
                    $column .= '<small class="badge badge-secondary">' . $row->status . '</small>';
                }

                if ($row->status == config('global.type.status.red')) {
                    $column .= '<small class="badge badge-danger">' . $row->status . '</small>';
                }
                return $column;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if (($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $year = SecureHelper::unsecure($request->input('year'));
            if (!$year) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $division_id = SecureHelper::unsecure($request->input('division_id'));
            if (!$division_id) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $expense = Expense::select(['data_id'])->where('type', config('global.type.code.white'))->get()->toArray();
            $expense = array_column($expense, 'data_id');
            $data = Data::select(['id', 'ma_id', 'description', 'amount'])->whereNotIn('id', $expense)->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('input', 'remain');

            $table->addColumn('input', function ($row) {
                $expense =  Expense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->groupBy('data_id')->first();
                $amount = $this->convertAmount($row->amount, true);
                if ($expense) {
                    $amount = $this->convertAmount($row->amount, true) - $this->convertAmount($expense->amount, true);
                }
                $column = '<div class="form-check">
                <input class="form-check-input" type="radio" name="ma" id="ma' . $row->ma_id . '" value="' . SecureHelper::secure($row->id) . '" data-available="' . $amount . '" data-amount="' . $this->convertAmount($row->amount, true) . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
            });

            $table->addColumn('remain', function ($row) {
                $expense =  Expense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->groupBy('data_id')->first();
                $amount = $row->amount;
                if ($expense) {
                    $amount = $this->convertAmount($row->amount, true) - $this->convertAmount($expense->amount, true);
                    $amount = $this->convertAmount($amount);
                }

                return $amount;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog('DARA');

            return $table->toJson();
        }
    }

    public function getPic(Request $request)
    {
        if (($request->input('data_id') == null && $request->input('data_id') == '')) {
            $response = new Response();
            $response->setData([['id' => '', 'text' => '-- Silakan Pilih --']]);
            return response()->json($response->responseJson());
        }

        $data_id = SecureHelper::unsecure($request->input('data_id'));
        if (!$data_id) {
            $response = new Response();
            $response->setData([['id' => '', 'text' => '-- Silakan Pilih --']]);
            return response()->json($response->responseJson());
        }

        $map = MapData::where('data_id', $data_id)->get()->toArray();
        $data = array(['id' => '', 'text' => '-- Silakan Pilih --']);
        foreach ($map as $value) {
            $data[] = array(
                'id' => $value['staff_id'],
                'text' => $value['staff'],
            );
        }

        $response = new Response();
        $response->setData($data);
        return response()->json($response->responseJson());
    }

    public function view($id)
    {
        if (!$this->hasPrivilege($this->_readid)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Expense::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;

        $data['data'] = Data::find($data['data_id'])->toArray();

        $view = ['is_update' => $this->hasPrivilege($this->_update)];

        $this->writeAppLog($this->_readid, 'Expense : ' . $data['ma_id']);

        return view('contents.expense.view', array_merge($data, $view));
    }

    public function post(Request $request, $action, $id)
    {
        if (!in_array($action, config('global.action.form'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if (in_array($action, Arr::only(config('global.action.form'), ['add', 'edit']))) {
            $param = $request->all();

            if ($action === config('global.action.form.add')) {
                if (!$this->hasPrivilege($this->_create)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                if ($param['type'] == config('global.type.code.red')) {
                    $validateParams = [
                        'type' => 'required',
                        'expense_id' => 'required',
                        'expense_date' => 'required',
                        'reff_no' => 'required',
                        'reff_date' => 'required',
                        'description' => 'required',
                        'data_id' => 'required',
                        'name' => 'required',
                        'staff_id' => 'required',
                        'amount' => 'required',
                        'text_amount' => 'required',
                        'apply_date' => 'required',
                        'account' => 'required',
                        'image' => 'required|mimes:pdf,png,jpg',
                    ];
                } else {
                    $validateParams = [
                        'type' => 'required',
                        'expense_id' => 'required',
                        'expense_date' => 'required',
                        'reff_no' => 'required',
                        'reff_date' => 'required',
                        'description' => 'required',
                        'data_id' => 'required',
                        'name' => 'required',
                        'staff_id' => 'required',
                        'amount' => 'required',
                        'text_amount' => 'required',
                        'account' => 'required',
                    ];
                }

                $validator = Validator::make($param, $validateParams);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->all() as $val) {
                        $response = new Response(false, $val);
                        return response()->json($response->responseJson());
                    }
                } else {
                    if (!in_array($param['type'], config('global.type.code'))) {
                        $response = new Response();
                        return response()->json($response->responseJson());
                    }

                    $data_id = SecureHelper::unsecure($param['data_id']);
                    $image = null;
                    $apply_date = null;

                    if (isset($param['image']) && $param['image'] != '') {
                        $file = $request->file('image');
                        $filename = date('d_M_Y_H_i_s') . '_' . $file->getClientOriginalName();
                        $descMonth = config('global.months');

                        $today = Carbon::now();
                        $year = $today->year;
                        $month = $today->month;
                        $month = $month < 10 ? '0' . $month : $month;

                        $pathYear = public_path('upload') . '/' . $year;
                        $pathMonth = public_path('upload') . '/' . $year . '/' . $descMonth[$month];

                        if (!File::exists($pathYear)) {
                            File::makeDirectory($pathYear, 0777, true, true);
                        }

                        if (!File::exists($pathMonth)) {
                            File::makeDirectory($pathMonth, 0777, true, true);
                        }

                        if ($file->move($pathMonth, $filename)) {
                            $image = $filename;
                            $apply_date = $param['apply_date'];
                        } else {
                            $response = new Response(false, 'Gagal Mengunggah File Ke Server');
                            return response()->json($response->responseJson());
                        }
                    }

                    $expense = Expense::create([
                        'type' => $param['type'],
                        'expense_id' => $param['expense_id'],
                        'expense_date' => $param['expense_date'],
                        'reff_no' => $param['reff_no'],
                        'reff_date' => $param['reff_date'],
                        'description' => $param['description'],
                        'sub_description' => $param['sub_description'],
                        'data_id' => $data_id,
                        'name' => $param['name'],
                        'staff_id' => $param['staff_id'],
                        'amount' => $param['amount'],
                        'text_amount' => $param['text_amount'],
                        'account' => $param['account'],
                        'apply_date' => $apply_date,
                        'image' => $image,
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);

                    if ($expense->id) {
                        $division_id = SecureHelper::unsecure($param['data_id']);
                        $balance = Balance::where('division_id', $division_id)->where('is_trash', 0)->first();
                        if ($balance) {
                            $balance->amount = ($this->convertAmount($balance->amount, true) - $this->convertAmount($param['amount'], true));
                            $balance->updated_by = Auth::user()->username;
                            if ($balance->save()) {
                                HistoryBalance::create([
                                    'balance_id' => $balance->id,
                                    'amount' => $param['amount'],
                                    'description' => $param['description'],
                                    'transaction_id' => config('global.transaction.code.debet')
                                ]);
                            }
                        }

                        $response = new Response(true, __('Expense created successfuly'), 1);
                        $response->setRedirect(route('transaction.expense.view', ['id' => SecureHelper::secure($expense->id)]));

                        $this->writeAppLog($this->_create, 'Expense : ' . $param['expense_id']);
                    } else {
                        $response = new Response(false, __('Expense create failed. Please try again'));
                    }
                }
            }

            if ($action === config('global.action.form.edit')) {
                if (!$this->hasPrivilege($this->_update)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $plainId = SecureHelper::unsecure($id);
                if (!$plainId) {
                    $response = new Response();
                    return response()->json($response->responseJson());
                }

                if (!$this->hasPrivilege($this->_readid)) {
                } else {
                    if ($param['type'] == config('global.type.code.red')) {
                        $validateParams = [
                            'type' => 'required',
                            'expense_id' => 'required',
                            'expense_date' => 'required',
                            'reff_no' => 'required',
                            'reff_date' => 'required',
                            'description' => 'required',
                            'data_id' => 'required',
                            'name' => 'required',
                            'staff_id' => 'required',
                            'amount' => 'required',
                            'text_amount' => 'required',
                            'apply_date' => 'required',
                            'account' => 'required',
                            'image' => 'mimes:pdf,png,jpg',
                        ];
                    } else {
                        $validateParams = [
                            'type' => 'required',
                            'expense_id' => 'required',
                            'expense_date' => 'required',
                            'reff_no' => 'required',
                            'reff_date' => 'required',
                            'description' => 'required',
                            'data_id' => 'required',
                            'name' => 'required',
                            'staff_id' => 'required',
                            'amount' => 'required',
                            'text_amount' => 'required',
                            'account' => 'required',
                        ];
                    }

                    $validator = Validator::make($param, $validateParams);

                    if ($validator->fails()) {
                        $errors = $validator->errors();
                        foreach ($errors->all() as $val) {
                            $response = new Response(false, $val);
                            return response()->json($response->responseJson());
                        }
                    } else {
                        if (!in_array($param['type'], config('global.type.code'))) {
                            $response = new Response(false, $val);
                            return response()->json($response->responseJson());
                        }

                        $image = null;
                        $apply_date = null;

                        if (isset($param['image']) && $param['image'] != '') {
                            $file = $request->file('image');
                            $filename = date('d_M_Y_H_i_s') . '_' . $file->getClientOriginalName();
                            $descMonth = config('global.months');

                            $today = Carbon::now();
                            $year = $today->year;
                            $month = $today->month;
                            $month = $month < 10 ? '0' . $month : $month;

                            $pathYear = public_path('upload') . '/' . $year;
                            $pathMonth = public_path('upload') . '/' . $year . '/' . $descMonth[$month];

                            if (!File::exists($pathYear)) {
                                File::makeDirectory($pathYear, 0777, true, true);
                            }

                            if (!File::exists($pathMonth)) {
                                File::makeDirectory($pathMonth, 0777, true, true);
                            }

                            if ($file->move($pathMonth, $filename)) {
                                $image = $filename;
                                $apply_date = $param['apply_date'];
                            } else {
                                $response = new Response(false, 'Gagal Mengunggah File Ke Server');
                                return response()->json($response->responseJson());
                            }
                        }

                        $expense = Expense::find($plainId);
                        $expense->expense_date = $param['expense_date'];
                        $expense->reff_no = $param['reff_no'];
                        $expense->reff_date = $param['reff_date'];
                        $expense->description = $param['description'];
                        $expense->name = $param['name'];
                        $expense->staff_id = $param['staff_id'];
                        $expense->amount = $param['amount'];
                        $expense->text_amount = $param['text_amount'];
                        $expense->account = $param['account'];
                        $expense->apply_date = $apply_date;
                        $expense->image = $image;
                        $expense->updated_by = Auth::user()->username;
                    }
                }
            }
        }

        return response()->json($response->responseJson());
    }

    private function generate()
    {
        $prefix = 'PM';
        $year = date('y');
        $number = '0001';

        $expense = Expense::select('expense_id')->where('expense_id', 'like', '%' . $prefix . $year . '%')->orderBy('expense_id', 'desc')->first();
        if ($expense) {
            $currentNumber = Str::after($expense->expense_id, $prefix . $year);
            $currentNumber++;
            $currentNumber = strval($currentNumber);
            $temp = '';
            for ($i = 0; $i < 4 - strlen($currentNumber); $i++) {
                $temp .= '0';
            }

            $number = $temp . $currentNumber;
        }

        return $prefix . $year . $number;
    }
}
