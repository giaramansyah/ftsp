<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\MapData;
use App\Models\Expense;
use App\Models\MapExpense;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class DataController extends Controller
{
    protected $_create = 'DACR';
    protected $_update = 'DAUP';
    protected $_delete = 'DARM';
    protected $_readall = 'DARA';
    protected $_readid = 'DARD';

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

        return view('contents.data.index', $view);
    }

    public function add()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $year = $this->getYears();

        $view = ['yearArr' => $year, 'action' => route('master.data.upload'), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.data.upload', $view);
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

        $data = Data::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }
        
        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('ma_id' => $data['ma_id']);
        }

        $year = $this->getYears();
        $division = $this->getDivisions();
        $staff = $this->getStaffs();
        $staff = Arr::except($staff, 0);

        $view = ['yearArr' => $year, 'divisionArr' => $division, 'staffArr' => $staff, 'action' => route('master.data.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'mandatory' => $this->hasPrivilege($this->_readid)];

        return view('contents.data.form', array_merge($data, $view));
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

        $data = Data::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;

        $expense = MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $plainId)->first()->toArray();
        if ($expense) {
            $used = $expense['amount'];
        } else {
            $used = 0;
        }
        $total = $this->convertAmount($data['amount'], true);
        $remain = ($total - $used);
        $percent = round(($used / $total) * 100, 2);

        $data['used'] = $this->convertAmount($used);
        $data['remain'] = $this->convertAmount($remain);
        $data['percent'] = $percent . '%';

        $map = MapExpense::where('data_id', $plainId)->get()->toArray();
        $map = array_column($map, 'expense_id');
        
        $data['history'] = Expense::whereIn('id', $map)->get()->toArray();

        $view = ['is_update' => $this->hasPrivilege($this->_update), 'is_delete' => $this->hasPrivilege($this->_delete)];

        $this->writeAppLog($this->_readid, 'Data : ' . $data['ma_id']);

        return view('contents.data.view', array_merge($data, $view));
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

            
            $data = Data::select(['id', 'ma_id', 'description', 'amount', 'updated_at'])->where('is_trash', 0)->where('year', $year)->where('division_id', $division)->orderBy('ma_id');
            if(Auth::user()->staff_id != config('global.staff.code.admin')) {
                $map = MapData::where('staff_id',  Auth::user()->staff_id)->get()->toArray();
                $map = array_column($map, 'data_id');
                $data->whereIn('id', $map);
            }
            $data->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('ma', 'used', 'remain', 'percent');
            $table->addIndexColumn();

            $table->addColumn('ma', function ($row) {
                if ($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="' . route('master.data.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->ma_id . '</a>';
                } else {
                    $column = $row->ma_id();
                }

                return $column;
            });

            $table->addColumn('used', function ($row) {
                $expense = MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->first();
                if ($expense) {
                    $column = $this->convertAmount($expense->amount);
                } else {
                    $column = '0';
                }

                return $column;
            });

            $table->addColumn('remain', function ($row) {
                $expense = MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->first();
                if ($expense) {
                    $used = $expense->amount;
                    $total = $this->convertAmount($row->amount, true);
                    $column = $this->convertAmount($total - $used);
                } else {
                    $column = $row->amount;
                }

                return $column;
            });

            $table->addColumn('percent', function ($row) {
                $expense = MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $row->id)->first();
                if ($expense->amount > 0) {
                    $used = $expense->amount;
                    $total = $this->convertAmount($row->amount, true);
                    $column = round(($used / $total) * 100, 2);
                } else {
                    $column = '0.00';
                }

                return $column . '%';
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function upload(Request $request)
    {
        $param = $request->all();
        $validator = Validator::make($param, [
            'file' => 'required|mimes:xls,xlsx,csv',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('file')) {
                $response = new Response(false, 'Ekstensi File Tidak Valid');
            }
        } else {
            $file = $request->file('file');
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
                $this->writeDataLog($filename);
                $filepath = $pathMonth . '/' . $filename;

                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($filepath);
                $collections = array();
                $error = false;
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($sheet->getIndex() === 0) {
                        foreach ($sheet->getRowIterator() as $index => $row) {
                            if ($index == 1) {
                                if ($this->validateColumn($row->toArray(), $validator) === false) {
                                    $error = true;
                                    break;
                                }
                            } else {
                                $collection = $this->collectColumn($row->toArray(), $validator);
                                if ($collection !== false) {
                                    $collections[] = $collection;
                                }
                            }
                        }
                        break;
                    }
                }

                if ($error) {
                    $response = new Response(false, 'Kolom Tabel Excel Tidak Sesuai Format');
                } else {
                    $arrDivision = config('global.division.code');
                    $arrStaff = array_combine(config('global.staff.raw'), config('global.staff.code'));

                    $data = Data::where('year', $param['year'])->get();
                    foreach($data as $value) {
                        $delete = Data::find($value->id);
                        $delete->is_trash = 1;
                        $delete->save();
                    }

                    foreach ($collections as $collection) {
                        $staff = Str::lower(str_replace(" ", "", $collection['staff']));
                        $division = Str::lower(str_replace(" ", "", $collection['division']));
                        $data = Data::create([
                            'ma_id' => $collection['ma_id'],
                            'description' => trim($collection['description']),
                            'year' => $param['year'],
                            'division_id' => isset($arrDivision[$division]) ? $arrDivision[$division] : 0,
                            'amount' => $collection['amount'],
                            'filename' => $filename,
                            'created_by' => Auth::user()->username,
                            'updated_by' => Auth::user()->username,
                        ]);
                        if ($data->id) {
                            $arr = explode(',', $staff);
                            foreach ($arr as $value) {
                                MapData::create([
                                    'data_id' => $data->id,
                                    'staff_id' => isset($arrStaff[$value]) ? $arrStaff[$value] : 0
                                ]);
                            }
                        }
                    }

                    $response = new Response(true, __('File Uploaded successfuly'), 1);
                    $response->setRedirect(route('master.data.index'));

                    $this->writeAppLog($this->_create, 'Upload File : ' . $file->getClientOriginalName());
                }
            } else {
                $response = new Response(false, 'Gagal Mengunggah File Ke Server');
            }
        }

        return response()->json($response->responseJson());
    }

    public function post(Request $request, $action, $id)
    {
        if(!in_array($action, config('global.action.form'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if($action === config('global.action.form.edit')) {
            $param = SecureHelper::unpack($request->input('json'));

            if (!is_array($param)) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if(!$this->hasPrivilege($this->_update)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $plainId = SecureHelper::unsecure($id);

            if(!$plainId) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if(!$this->hasPrivilege($this->_readid)) {
                $data = Data::find($plainId);

                if(isset($param['year']) && $param['year'] != '') $data->year = $param['year'];
                if(isset($param['description']) && $param['description'] != '') $data->description = $param['description'];
                if(isset($param['division_id']) && $param['division_id'] != '') $data->division_id = $param['division_id'];
                if(!is_array($param['staff_id'])) $param['staff_id'] = array($param['staff_id']);
                $data->updated_by = Auth::user()->username;

                if($data->save()) {
                    $map = MapData::where('data_id', $plainId);
                    $map->forceDelete();
                    foreach($param['staff_id'] as $value) {
                        MapData::create([
                            'data_id' => $plainId,
                            'staff_id' => $value
                        ]);
                    }

                    $response = new Response(true, __('Data updated successfuly'), 1);
                    $response->setRedirect(route('master.data.index'));

                    $this->writeAppLog($this->_update, 'Data : '.$param['ma_id']);
                } else {
                    $response = new Response(false, __('Data update failed. Please try again'));
                }
            } else {
                $data = Data::find($plainId);
                $data->year = $param['year'];
                $data->description = $param['description'];
                $data->division_id = $param['division_id'];
                $data->updated_by = Auth::user()->username;

                if(!is_array($param['staff_id'])) $param['staff_id'] = array($param['staff_id']);

                if($data->save()) {
                    $map = MapData::where('data_id', $plainId);
                    $map->forceDelete();
                    foreach($param['staff_id'] as $value) {
                        MapData::create([
                            'data_id' => $plainId,
                            'staff_id' => $value
                        ]);
                    }
                    
                    $response = new Response(true, __('Data updated successfuly'), 1);
                    $response->setRedirect(route('master.data.index'));

                    $this->writeAppLog($this->_update, 'Data : '.$param['ma_id']);
                } else {
                    $response = new Response(false, __('Data update failed. Please try again'));
                }
            }

        }

        if($action === config('global.action.form.delete')) {


            if(!$this->hasPrivilege($this->_delete)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $plainId = SecureHelper::unsecure($id);

            if(!$plainId) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            $data = Data::find($plainId);
            $data->is_trash = 1;

            if($data->save()) {
                $response = new Response(true, __('Data deleted successfuly'), 1);
                $response->setRedirect(route('master.data.index'));

                $this->writeAppLog($this->_delete, 'Data Account : '.$data->ma_id);
            } else {
                $response = new Response(false, __('Account delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }

    private function collectColumn($row)
    {
        $validator = config('global.validation');
        $collection = array();
        $index = 0;
        foreach ($validator['columns'] as $key => $column) {
            $string = preg_replace($validator['regex'][$key], '', strval($row[$index]));
            $string = substr($string, 0, $validator['limitter'][$key]);
            if ($string == '') {
                return false;
            }
            $collection[$key] = $string;
            $index++;
        }

        return $collection;
    }

    private function validateColumn($header)
    {
        $validator = array_values(config('global.validation.columns'));
        $valid = true;
        foreach ($validator as $key => $value) {
            if (Str::upper($header[$key]) != Str::upper($value)) {
                $valid = false;
                break;
            }
        }

        return $valid;
    }
}
