<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Expense;
use App\Models\HistoryBalance;
use App\Models\MapData;
use App\Models\MapExpense;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $employees = $this->getEmployees();
        $expense_id = $this->generate();

        $is_red = false;
        if ($type == config('global.type.code.red')) {
            $is_red = true;
        }

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'employeeArr' => $employees, 'type' => $type, 'expense_id' => $expense_id, 'action' => route('transaction.expense.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'is_red' => $is_red, 'typeDesc' => $typeDesc, 'mandatory' => $this->hasPrivilege($this->_create)];

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

        $map = MapExpense::where('expense_id', $plainId)->get()->toArray();
        $data_id = array_column($map, 'data_id');
        $data['data'] = Data::where('id', $data_id[0])->first()->toArray();
        if($data['is_multiple'] == 1) {
            $description = array();
            $amount = 0;

            $datas = Data::whereIn('id', $data_id)->get();
            foreach($datas as $value) {
                $description[] = $value->description;
                $amount += $this->convertAmount($value->amount, true);
            } 

            $data['data']['description'] = implode('<br>', $description);
            $data['data']['amount'] = $this->convertAmount($amount);
        }

        $map = MapData::where('data_id', $data_id[0])->get()->toArray();
        $staffArr = array();
        foreach ($map as $value) {
            $staffArr[] = array(
                'id' => $value['staff_id'],
                'name' => $value['staff'],
            );
        }

        $map =  MapExpense::whereNotIn('expense_id', [$plainId])->whereIn('data_id', $data_id)->get()->toArray();
        $expense_id = array_column($map, 'expense_id');
        $expense = Expense::selectRaw('sum(amount) as amount')->whereIn('id', $expense_id)->first();

        $data['data']['available'] = $this->convertAmount($data['data']['amount'], true);
        if ($expense) {
            $data['data']['available'] = $this->convertAmount($data['data']['amount'], true) - $this->convertAmount($expense->amount, true);
        }
        $data['data']['remain'] = $this->convertAmount($data['data']['available']);


        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $typeDesc = $types[$data['type']];

        $is_red = false;
        if ($data['type'] == config('global.type.code.red')) {
            $is_red = true;
            $file = $this->getFile($data['image'], public_path('upload'));
            $image = $data['image'];
            $data['image'] = $file->name;
            $data['download'] = route('transaction.expense.download', ['id' => SecureHelper::pack(['file' => $image, 'path' => public_path('upload')])]);
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('expense_id' => $data['expense_id']);
        }

        $employees = $this->getEmployees();

        $view = ['staffArr' => $staffArr, 'employeeArr' => $employees, 'action' => route('transaction.expense.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'is_red' => $is_red, 'typeDesc' => $typeDesc, 'mandatory' => $this->hasPrivilege($this->_update)];

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

            $map = MapExpense::whereIn('data_id', $data)->groupBy('expense_id')->get()->toArray();
            $map = array_column($map, 'expense_id');

            $expense = Expense::select(['id', 'expense_id', 'ma_id', 'expense_date', 'reff_no', 'reff_date', 'staff_id', 'amount', 'type', 'updated_at', 'status'])->whereIn('id', $map)->orderBY('updated_at', 'desc');
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
                if ($row->status == config('global.status.code.unfinished')) {
                    $column .= '<small class="badge badge-secondary">' . $row->status_desc . '</small>';
                }

                if ($row->status == config('global.status.code.finished')) {
                    $column .= '<small class="badge badge-danger">' . $row->status_desc . '</small>';
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

            $data = Data::select(['id', 'ma_id', 'description', 'amount'])->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('input', 'remain');

            $table->addColumn('input', function ($row) {
                $expense = MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->first();
                $amount = $this->convertAmount($row->amount, true);
                if ($expense) {
                    $amount = $this->convertAmount($row->amount, true) - $expense->amount;
                }
                $column = '<div class="form-check">
                <input class="form-check-input" type="checkbox" name="data_id[]" id="ma' . $row->ma_id . '" value="' . SecureHelper::secure($row->id) . '" data-ma="' . $row->ma_id . '" data-available="' . $amount . '" data-amount="' . $this->convertAmount($row->amount, true) . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
            });

            $table->addColumn('remain', function ($row) {
                $expense =  MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->first();
                $amount = $row->amount;
                if ($expense) {
                    $amount = $this->convertAmount($row->amount, true) - $expense->amount;
                    $amount = $this->convertAmount($amount);
                }

                $amount = $amount < 0 ? '0' : $amount;

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

        if($data['is_multiple'] == 1) {
            $map = MapExpense::where('expense_id', $plainId)->get()->toArray();
            $data_id = array_column($map, 'data_id');
            
            $description = array();
            $amount = 0;
            $data['data'] = Data::where('id', $data_id[0])->first()->toArray();

            $datas = Data::whereIn('id', $data_id)->get();
            foreach($datas as $value) {
                $description[] = $value->description;
                $amount += $this->convertAmount($value->amount, true);
            } 

            $data['data']['description'] = implode('<br>', $description);
            $data['data']['amount'] = $this->convertAmount($amount);
        } else {
            $map = MapExpense::where('expense_id', $plainId)->first();
            $data['data'] = Data::where('id', $map->data_id)->first()->toArray();
        }

        $is_red = false;
        if ($data['status'] == config('global.status.code.finished')) {
            $is_red = true;
            $file = $this->getFile($data['image'], public_path('upload'));
            $image = $data['image'];
            $data['image'] = $file->name;
            $data['download'] = route('transaction.expense.download', ['id' => SecureHelper::pack(['file' => $image, 'path' => public_path('upload')])]);
        }

        $employeeArr = $this->getEmployees();

        $view = ['employeeArr' => $employeeArr, 'is_red' => $is_red, 'is_update' => $this->hasPrivilege($this->_update)];

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
                        'is_multiple' => 'required',
                        'image' => 'required',
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
                        'is_multiple' => 'required',
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
                        'status' => $param['status'],
                        'expense_id' => $param['expense_id'],
                        'expense_date' => $param['expense_date'],
                        'reff_no' => $param['reff_no'],
                        'reff_date' => $param['reff_date'],
                        'description' => $param['description'],
                        'sub_description' => $param['sub_description'],
                        'ma_id' => $param['ma_id'],
                        'name' => $param['name'],
                        'staff_id' => $param['staff_id'],
                        'amount' => $param['amount'],
                        'text_amount' => $param['text_amount'],
                        'account' => $param['account'],
                        'is_multiple' => $param['is_multiple'],
                        'apply_date' => $apply_date,
                        'image' => $image,
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);

                    if ($expense->id) {
                        $totalAmount = $this->convertAmount($param['amount'], true);

                        foreach($param['data_id'] as $value) {
                            $value = SecureHelper::unsecure($value);

                            if(!$value) {
                                continue;
                            }

                            $map = [
                                'expense_id' => $expense->id,
                                'data_id' => $value,
                                'amount' => 0
                            ];

                            if($param['is_multiple'] == 1) {
                                $data = Data::find($value);
                                if($data) {
                                    $amount = $this->convertAmount($data->amount, true);
                                    if($amount < $totalAmount) {
                                        $map['amount'] = $amount;
                                        $totalAmount -= $amount;
                                    } else {
                                        $map['amount'] = $totalAmount;
                                    }
                                }
                            } else {
                                $map['amount'] = $totalAmount;
                            }

                            MapExpense::create($map);
                        }

                        $division_id = SecureHelper::unsecure($param['division_id']);
                        $balance = Balance::where('division_id', $division_id)->where('is_trash', 0)->first();
                        if ($balance) {
                            $balance->amount = ($this->convertAmount($balance->amount, true) - $this->convertAmount($param['amount'], true));
                            $balance->updated_by = Auth::user()->username;
                            if ($balance->save()) {
                                HistoryBalance::create([
                                    'balance_id' => $balance->id,
                                    'amount' => $param['amount'],
                                    'description' => $param['description'],
                                    'data_id' => $expense->id,
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
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                } else {
                    if ($param['type'] == config('global.type.code.red')) {
                        $validateParams = [
                            'expense_date' => 'required',
                            'reff_no' => 'required',
                            'reff_date' => 'required',
                            'description' => 'required',
                            'ma_id' => 'required',
                            'name' => 'required',
                            'staff_id' => 'required',
                            'amount' => 'required',
                            'text_amount' => 'required',
                            'account' => 'required',
                            'apply_date' => 'required',
                        ];
                    } else {
                        $validateParams = [
                            'expense_date' => 'required',
                            'reff_no' => 'required',
                            'reff_date' => 'required',
                            'description' => 'required',
                            'ma_id' => 'required',
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
                        $status = null;

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
                                if($param['type'] == config('global.type.code.white') && $param['status'] == config('global.status.code.unfinished')) {
                                    $status = config('global.status.code.finished');
                                }
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
                        $expense->sub_description = $param['sub_description'];
                        $expense->ma_id = $param['ma_id'];
                        $expense->name = $param['name'];
                        $expense->staff_id = $param['staff_id'];
                        $expense->amount = $param['amount'];
                        $expense->text_amount = $param['text_amount'];
                        $expense->account = $param['account'];
                        if ($apply_date) $expense->apply_date = $apply_date;
                        if ($image) $expense->image = $image;
                        if ($status) $expense->status = $status;
                        $expense->updated_by = Auth::user()->username;

                        if ($expense->save()) {
                            $totalAmount = $this->convertAmount($param['amount'], true);

                            $map = MapExpense::where('expense_id', $plainId)->get();
                            foreach($map as $value){
                                if($expense->is_multiple == 1) {
                                    $data = Data::find($value->data_id);
                                    if($data) {
                                        $amount = $this->convertAmount($data->amount, true);
                                        if($amount < $totalAmount) {
                                            $value->amount = $amount;
                                            $totalAmount -= $amount;
                                        } else {
                                            $value->amount = $totalAmount;
                                        }
                                    }
                                } else {
                                    $value->amount = $totalAmount;
                                }

                                MapExpense::where('expense_id', $value->expense_id)->where('data_id', $value->data_id)->update(['amount' => $value->amount]);
                            }

                            $division_id = $param['division_id'];
                            $balance = Balance::where('division_id', $division_id)->where('is_trash', 0)->first();
                            if ($balance) {
                                $history = HistoryBalance::where('balance_id', $balance->id)->where('data_id', $expense->id)->where('transaction_id', config('global.transaction.code.debet'))->first();
                                $balance->amount = ($this->convertAmount($balance->amount, true) + $this->convertAmount($history->amount, true) - $this->convertAmount($param['amount'], true));
                                $balance->updated_by = Auth::user()->username;
                                if ($balance->save()) {
                                    $history->amount = $param['amount'];
                                    $history->save();
                                }
                            }

                            $response = new Response(true, __('Expense updated successfuly'), 1);
                            $response->setRedirect(route('transaction.expense.view', ['id' => SecureHelper::secure($expense->id)]));

                            $this->writeAppLog($this->_create, 'Expense : ' . $param['expense_id']);
                        } else {
                            $response = new Response(false, __('Expense updated failed. Please try again'));
                        }
                    }
                }
            }
        }

        return response()->json($response->responseJson());
    }

    public function print(Request $request, $id)
    {
        if (!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }

        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Expense::find($plainId)->toArray();
        $data['knowing'] = $param['knowing'];
        $data['approver'] = $param['approver'];
        $data['sender'] = $param['sender'];
        $data['reciever'] = $param['reciever'];

        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $filename = date('d_M_Y_H_i_s') . '_' . $types[$data['type']] . ' ' . $data['ma_id'] . '.pdf';
        $pdf = Pdf::loadView('partials.print.red', $data);

        $descMonth = config('global.months');

        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $month = $month < 10 ? '0' . $month : $month;

        $pathYear = public_path('download') . '/' . $year;
        $pathMonth = public_path('download') . '/' . $year . '/' . $descMonth[$month];

        if (!File::exists($pathYear)) {
            File::makeDirectory($pathYear, 0777, true, true);
        }

        if (!File::exists($pathMonth)) {
            File::makeDirectory($pathMonth, 0777, true, true);
        }

        $pdf->setPaper([0, 0, 680.315, 396.85], 'portrait');
        $pdf->save($pathMonth . '/' . $filename);

        $id = SecureHelper::pack(['file' => $filename, 'path' => public_path('download')]);

        $response = new Response(true, 'Report successfuly printed', 1);
        $response->setRedirect(route('transaction.expense.download', ['id' => $id]));

        return response()->json($response->responseJson());
    }

    public function download($id)
    {
        if (!$this->hasPrivilege($this->_readid)) {
            return abort(404);
        }

        $param = SecureHelper::unpack($id);

        if (!is_array($param)) {
            return abort(404);
        }

        $file = $this->getFile($param['file'], $param['path']);

        $headers = array(
            'Content-Type: application/pdf',
            'Content-Disposition: attachment;filename=' . $file->name,
            'Cache-Control: max-age=0',
            'Pragma: no-cache',
            'Expires: 0'
        );

        return response()->download($file->path, $headers);
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
