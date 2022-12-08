<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\Expense;
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

        return view('contents.expense.index', ['yearArr' =>  $years, 'year' => $currYear, 'is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function add($type)
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }
        
        if(!in_array($type, config('global.type.code'))) {
            return abort(404);
        }
        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $typeDesc = $types[$type];
        $years = $this->getYears();
        $divisions = $this->getDivisions();
        $expense_id = $this->generate();

        $is_red = false;
        if($type == config('global.type.code.red')) {
            $is_red = true;
        }

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'type' => $type, 'expense_id' => $expense_id, 'action' => route('transaction.expense.post', ['action' => config('global.action.form.add'), 'id' => 0 ]), 'is_red' => $is_red, 'typeDesc' => $typeDesc, 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.expense.form', $view);
    }

    public function edit($id)
    {
    }

    public function getList(Request $request)
    {
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

                $validator = Validator::make($param, [
                    'type' => 'type',
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
                    'image' => 'mimes:pdf,png,jpg',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->all() as $val) {
                        $response = new Response(false, $val);
                        return response()->json($response->responseJson());
                    }
                } else {
                    if(!in_array($param['type'], config('global.type.code'))) {
                        return abort(404);
                    }

                    $data_id = SecureHelper::unsecure($param['data_id']);
                    $image = null;
                    $apply_date = null;
                    $status = 0;

                    if(isset($param['image']) && $param['image'] != '') {
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
                            $status = 1;
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
                        'status' => $status,
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);
                    
                    if($expense->id) {
                        $response = new Response(true, __('Expense created successfuly'), 1);
                        $response->setRedirect(route('transaction.expense.index'));

                        $this->writeAppLog($this->_create, 'Expense : '.$param['expense_id']);
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
                    
                }
            }
        }

        return response()->json($response->responseJson());
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if(($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $year = SecureHelper::unsecure($request->input('year'));
            if(!$year) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $division_id = SecureHelper::unsecure($request->input('division_id'));
            if(!$division_id) {
                $data = Expense::where('expense_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $data = Data::select(['id', 'ma_id', 'description', 'amount'])->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('input', 'remain');

            $table->addColumn('input', function($row) {
                $expense =  Expense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->groupBy('data_id')->first();
                if ($expense) {
                    $row->amount = $this->convertAmount($row->amount, true) - $this->convertAmount($expense->amount, true);
                }
                $column = '<div class="form-check">
                <input class="form-check-input" type="radio" name="ma" value="'.SecureHelper::secure($row->id).'" data-amount="'.$this->convertAmount($row->amount, true).'">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
            });

            $table->addColumn('remain', function($row) {
                $expense =  Expense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->groupBy('data_id')->first();
                if ($expense) {
                    $row->amount = $this->convertAmount($row->amount, true) - $this->convertAmount($expense->amount, true);
                }

                return $this->convertAmount($row->amount);
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog('DARA');

            return $table->toJson();
        }
    }

    public function getPic(Request $request)
    {
        if(($request->input('data_id') == null && $request->input('data_id') == '')) {
            $response = new Response();
            $response->setData([['id' => '', 'text' => '-- Silakan Pilih --']]);
            return response()->json($response->responseJson());
        }

        $data_id = SecureHelper::unsecure($request->input('data_id'));
        if(!$data_id) {
            $response = new Response();
            $response->setData([['id' => '', 'text' => '-- Silakan Pilih --']]);
            return response()->json($response->responseJson());
        }

        $map = MapData::where('data_id', $data_id)->get()->toArray();
        $data = array();
        foreach($map as $value) {
            $data[] = array(
                'id' => $value['staff_id'],
                'text' => $value['staff'],
            );
        }

        $response = new Response();
        $response->setData($data);
        return response()->json($response->responseJson());
    }

    private function generate()
    {
        $prefix = 'PM';
        $year = date('y');
        $number = '0001';

        $expense = Expense::select('expense_id')->where('expense_id', 'like', "%$$prefix$year%")->orderBy('expense_id', 'desc')->first();
        if($expense) {
            $currentNumber = Str::after($expense->expense_id, $prefix.$year);
            $currentNumber++;
            $currentNumber = strval($currentNumber);
            $temp = '';
            for($i = 0; $i < 4 - strlen($currentNumber); $i++) {
                $temp .= '0';
            }

            $number = $temp.$currentNumber;
        } 

        return $prefix.$year.$number;
    }
}
