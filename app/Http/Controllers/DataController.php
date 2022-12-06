<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\MapData;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function index() 
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.data.index');
    }

    public function add()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $year = $this->getYears();

        $view = ['yearArr' => $year, 'action' => route('master.data.upload'), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.data.upload', $view);
    }

    public function edit($id)
    {

    }

    public function view($id)
    {
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = Data::select(['id', 'ma_id', 'description', 'year', 'division_id', 'amount', 'updated_at'])->where('is_trash', 0)->orderBy('id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('ma');
            $table->addIndexColumn();

            $table->addColumn('ma', function($row) {
                if($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="'.route('master.data.view', ['id' => SecureHelper::secure($row->id)]).'">' . $row->ma_id . '</a>';
                } else {
                    $column = $row->ma_id();
                }

                return $column;
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
            $filename = date('d_M_Y_H_i_s').'_'.$file->getClientOriginalName();
            $descMonth = config('global.months');

            $today = Carbon::now();
            $year = $today->year;
            $month = $today->month;
            $month = $month < 10 ? '0'.$month : $month;

            $pathYear = public_path('upload').'/'.$year;
            $pathMonth = public_path('upload').'/'.$year.'/'.$descMonth[$month];

            if(!File::exists($pathYear)) {
                File::makeDirectory($pathYear, 0777, true, true);
            }

            if(!File::exists($pathMonth)) {
                File::makeDirectory($pathMonth, 0777, true, true);
            }

            if($file->move($pathMonth,$filename)) {
                $this->writeDataLog($filename);
                $filepath = $pathMonth.'/'.$filename;

                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($filepath);
                $collections = array();
                $error = false;
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($sheet->getIndex() === 0) {
                        foreach ($sheet->getRowIterator() as $index => $row) {
                            if($index == 1) {
                                if($this->validateColumn($row->toArray(), $validator) === false) {
                                    $error = true;
                                    break;
                                }
                            } else {
                                $collection = $this->collectColumn($row->toArray(), $validator);
                                if($collection !== false) {
                                    $collections[] = $collection;
                                }
                            }
                        }
                        break;
                    }
                }

                if($error) {
                    $response = new Response(false, 'Kolom Tabel Excel Tidak Sesuai Format');
                } else {
                    $arrDivision = config('global.division.code');
                    $arrStaff = array_combine(config('global.staff.raw'), config('global.staff.code'));
                    foreach($collections as $collection){
                        $staff = Str::lower(str_replace(" ", "", $collection['staff']));
                        $division = Str::lower(str_replace(" ", "", $collection['division']));
                        $data = Data::create([
                            'ma_id' => $collection['ma_id'],
                            'description' => $collection['description'],
                            'year' => $param['year'],
                            'division_id' => $arrDivision[$division],
                            'amount' => $collection['amount'],
                            'filename' => $filename,
                            'created_by' => Auth::user()->username,
                            'updated_by' => Auth::user()->username,
                        ]);
                        if($data->id) {
                            $arr = explode(',', $staff);
                            foreach($arr as $value) {
                                MapData::create([
                                    'data_id' => $data->id,
                                    'staff_id' => $arrStaff[$value]
                                ]);
                            }
                        }
                    }

                    $response = new Response(true, __('Privilege group created successfuly'), 1);
                    $response->setRedirect(route('master.data.index'));

                    $this->writeAppLog($this->_create, 'Upload File : '.$file->getClientOriginalName());
                }
            } else {
                $response = new Response(false, 'Gagal Mengunggah File Ke Server');
            }
        }

        return response()->json($response->responseJson());
    }

    private function collectColumn($row) 
    {
        $validator = config('global.validation');
        $collection = array();
        $index = 0;
        foreach($validator['columns'] as $key => $column) {
            $string = preg_replace($validator['regex'][$key], '', strval($row[$index]));
            $string = substr($string, 0, $validator['limitter'][$key]);
            if($string == '') {
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
        foreach($validator as $key => $value) {
            if(Str::upper($header[$key]) != Str::upper($value)) {
                $valid = false;
                break;
            }
        }

        return $valid;
    }
}
