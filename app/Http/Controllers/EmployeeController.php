<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Employee;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    protected $_create = 'EMCR';
    protected $_update = 'EMUP';
    protected $_delete = 'EMRM';
    protected $_readall = 'EMRA';
    protected $_readid = 'EMRD';

    public function index()
    {
        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.employee.index', ['is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function upload()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $view = ['action' => route('master.employee.postupload'), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.employee.upload', $view);
    }
    
    public function add()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $unit = $this->getUnits();

        $view = ['unitArr' => $unit, 'action' => route('master.employee.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.employee.form', $view);
    }

    public function edit($id)
    {
        if(!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }
        
        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Employee::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('id' => $data['id']);
        }

        $unit = $this->getUnits();

        $view = ['unitArr' => $unit, 'action' => route('master.employee.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'mandatory' => $this->hasPrivilege($this->_readid)];

        return view('contents.employee.form', array_merge($data, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = Employee::where('is_trash', '0');
            $table = DataTables::eloquent($data);
            $table->addIndexColumn();

            if($this->hasPrivilege($this->_update) || $this->hasPrivilege($this->_delete)) {
                $table->addColumn('action', function($row) {
                    $column = '';

                    if($this->hasPrivilege($this->_update)) {
                        $param = array('class' => 'btn-xs', 'action' => route('master.employee.edit', ['id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.edit', $param)->render();
                    }

                    if($this->hasPrivilege($this->_delete)) {
                        $param = array('class' => 'btn-xs', 'source' => 'table', 'action' => route('master.employee.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.delete', $param)->render();
                    }

                    return $column;
                });

                $table->rawColumns(['action']);
            }

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function post(Request $request, $action, $id)
    {
        if (!in_array($action, config('global.action.form'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if (in_array($action, Arr::only(config('global.action.form'), ['add', 'edit']))) {
            $param = SecureHelper::unpack($request->input('json'));

            if (!is_array($param)) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if ($action === config('global.action.form.add')) {
                if (!$this->hasPrivilege($this->_create)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $employee = Employee::where('nik', $param['nik'])->where('is_trash', 0)->first();
                if (!$employee) {
                    $employee = Employee::create([
                        'nik' => $param['nik'],
                        'unit_id' => $param['unit_id'],
                        'name' => $param['name'],
                        'account' => $param['account'],
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);
                    if ($employee->id) {
                        $response = new Response(true, __('Employee created successfuly'), 1);
                        $response->setRedirect(route('master.employee.index'));
                        $this->writeAppLog($this->_create, 'Employee Name : ' . $param['name']);
                    } else {
                        $response = new Response(false, __('Employee create failed. Please try again'));
                    }
                } else {
                    $response = new Response(false, __('Employee is already registered'));
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

                if(!$this->hasPrivilege($this->_readid)) {
                    $employee = Employee::find($plainId);
    
                    if(isset($param['nik']) && $param['nik'] != '') $employee->nik = $param['nik'];
                    if(isset($param['unit_id']) && $param['unit_id'] != '') $employee->unit_id = $param['unit_id'];
                    if(isset($param['name']) && $param['name'] != '') $employee->name = $param['name'];
                    if(isset($param['account']) && $param['account'] != '') $employee->account = $param['account'];
                    $employee->updated_by = Auth::user()->username;
    
                    if($employee->save()) {
                        $response = new Response(true, __('Employee updated successfuly'), 1);
                        $response->setRedirect(route('master.employee.index'));
    
                        $this->writeAppLog($this->_update, 'Employee Name : '.$employee->name);
                    } else {
                        $response = new Response(false, __('Employee update failed. Please try again'));
                    }
                } else {
                    $employee = Employee::find($plainId);
                    $employee->nik = $param['nik'];
                    $employee->unit_id = $param['unit_id'];
                    $employee->name = $param['name'];
                    $employee->account = $param['account'];
                    $employee->updated_by = Auth::user()->username;
    
                    if($employee->save()) {
                        $response = new Response(true, __('Employee updated successfuly'), 1);
                        $response->setRedirect(route('master.employee.index'));
    
                        $this->writeAppLog($this->_update, 'Employee Name : '.$employee->name);
                    } else {
                        $response = new Response(false, __('Employee update failed. Please try again'));
                    }
                }
            }
        }

        if ($action === config('global.action.form.delete')) {
            if (!$this->hasPrivilege($this->_delete)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $plainId = SecureHelper::unsecure($id);

            if (!$plainId) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            $employee = Employee::find($plainId);
            $employee->is_trash = 1;

            if ($employee->save()) {
                $response = new Response(true, __('Employee deleted successfuly'), 1);
                $response->setRedirect(route('master.employee.index'));
                $this->writeAppLog($this->_delete, 'Employee Name : ' . $employee->name);
            } else {
                $response = new Response(false, __('Employee delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }

    public function postUpload(Request $request)
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
                    $arrUnit = array_combine(config('global.unit.desc'), config('global.unit.code'));
                    foreach ($collections as $collection) {
                        $unit = Str::lower(str_replace(" ", "", $collection['unit_id']));
                        $data = Employee::create([
                            'nik' => $collection['nik'],
                            'unit_id' => $arrUnit[$unit],
                            'name' => trim($collection['name']),
                            'account' => $collection['account'],
                            'created_by' => Auth::user()->username,
                            'updated_by' => Auth::user()->username,
                        ]);
                    }

                    $response = new Response(true, __('File Uploaded successfuly'), 1);
                    $response->setRedirect(route('master.employee.index'));

                    $this->writeAppLog($this->_create, 'Upload File : ' . $file->getClientOriginalName());
                }
            } else {
                $response = new Response(false, 'Gagal Mengunggah File Ke Server');
            }
        }

        return response()->json($response->responseJson());
    }

    private function collectColumn($row)
    {
        $validator = config('global.employee');
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
        $validator = array_values(config('global.employee.columns'));
        $valid = true;
        foreach ($validator as $key => $value) {
            if (Str::upper(trim($header[$key])) != Str::upper($value)) {
                $valid = false;
                break;
            }
        }



        return $valid;
    }
}
