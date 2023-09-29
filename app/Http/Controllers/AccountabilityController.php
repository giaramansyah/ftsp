<?php

namespace App\Http\Controllers;

use App\Library\ExcelWriter;
use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\MapExpense;
use App\Models\MapReport;
use App\Models\Reception;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AccountabilityController extends Controller
{
    protected $_create = 'ACCR';

    public function index()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $yearArr = $this->getYears();
        $divisionArr = $this->getCompactDivisions();
        $employeeArr = $this->getEmployees();

        $view = ['yearArr' => $yearArr, 'divisionArr' => $divisionArr, 'employeeArr' => $employeeArr, 'mandatory' => $this->hasPrivilege($this->_create), 'action' => route('report.accountability.post')];

        return view('contents.accountability.index', $view);
    }

    public function getReception(Request $request)
    {
        if ($request->ajax()) {
            if (($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
                $data = Reception::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $year = SecureHelper::unsecure($request->input('year'));
            if (!$year) {
                $data = Reception::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $division_id = SecureHelper::unsecure($request->input('division_id'));
            if (!$division_id) {
                $data = Reception::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $data = Reception::where('year', $year);
            if($division_id != 99) {
                $data->where('division_id', $division_id);
            } else {
                $data->whereIn('division_id', [config('global.division.code.fakultas'), config('global.division.code.arsitektur'), config('global.division.code.sipil')]);
            }
            $table = DataTables::eloquent($data);
            $rawColumns = array('input');

            $table->addColumn('input', function ($row) {
                $column = '<div class="form-check">
                <input class="form-check-input" type="checkbox" name="reception" value="' . $row->id . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog('RERA');

            return $table->toJson();
        }
    }

    public function getExpense(Request $request)
    {
        if ($request->ajax()) {
            if (($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
                $data = Expense::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $year = SecureHelper::unsecure($request->input('year'));
            if (!$year) {
                $data = Expense::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $division_id = SecureHelper::unsecure($request->input('division_id'));
            if (!$division_id) {
                $data = Expense::where('reception_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $data = Data::select(['id'])->where('is_trash', 0)->where('year', $year);
            if($division_id != 99) {
                $data->where('division_id', $division_id);
            } else {
                $data->whereIn('division_id', [config('global.division.code.fakultas'), config('global.division.code.arsitektur'), config('global.division.code.sipil')]);
            }
            $data = $data->orderBy('id')->get()->toArray();
            $data = array_column($data, 'id');

            $map = MapExpense::whereIn('data_id', $data)->get()->toArray();
            $map = array_column($map, 'expense_id');

            $data = Expense::whereIn('id', $map);
            $table = DataTables::eloquent($data);
            $rawColumns = array('input', 'status_desc');

            $table->addColumn('input', function ($row) {
                $column = '<div class="form-check">
                <input class="form-check-input" type="checkbox" name="expense" value="' . $row->id . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

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

            $this->writeAppLog('EXRA');

            return $table->toJson();
        }
    }

    public function post(Request $request)
    {
        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }
        
        if (!$this->hasPrivilege($this->_create)) {
            $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
            return response()->json($response->responseJson());
        }
        
        $division_id = SecureHelper::unsecure($param['division_id']);
        if(!$division_id) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $year = SecureHelper::unsecure($param['year']);
        if(!$year) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $reportType =  SecureHelper::unpack($param['report_type']);
        if (!is_array($reportType)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }
        
        $report = Report::create([
            'year' => $year,
            'division_id' => $division_id,
            'report_date' => $param['accountability_date'],
            'type' => $reportType['type'],
            'knowing' => $param['knowing'],
            'created_by' => Auth::user()->username,
            'updated_by' => Auth::user()->username,
        ]);

        if($report->id) {
            if(isset($param['reception'])) {
                if(is_array($param['reception'])) {
                    foreach($param['reception'] as $value) {
                        MapReport::create([
                            'report_id' => $report->id,
                            'data_id' => $value,
                            'is_reception' => true,
                            'is_expense' => false,
                        ]);
                    }
                } else {
                    MapReport::create([
                        'report_id' => $report->id,
                        'data_id' => $param['reception'],
                        'is_reception' => true,
                        'is_expense' => false,
                    ]);
                }
            }

            if(isset($param['expense'])) {
                if(is_array($param['expense'])) {
                    foreach($param['expense'] as $value) {
                        MapReport::create([
                            'report_id' => $report->id,
                            'data_id' => $value,
                            'is_reception' => false,
                            'is_expense' => true,
                        ]);
                    }
                } else {
                    MapReport::create([
                        'report_id' => $report->id,
                        'data_id' => $param['expense'],
                        'is_reception' => false,
                        'is_expense' => true,
                    ]);
                }
            }

            $reports = array_combine(config('global.report.code'), config('global.report.desc'));
            $divisions = array_combine(config('global.compact_division.code'), config('global.compact_division.report'));
            $employee = Employee::find($param['knowing']);
            $employee2 = Employee::find($param['knowing2']);
            $date = Carbon::createFromFormat('Y-m-d', $param['accountability_date']);


            if($reportType['type'] == config('global.report.code.accountability_fakultas')) {
                $data = [
                    'header' => $divisions[$division_id],
                    'opening_balance' => 0,
                    'opening_balance_date' => $date->format('d F y'),
                    'closing_balance' => 0,
                    'closing_balance_date' => $date->format('d F y'),
                    'report_date' => $date->format('d F y'),
                    'reception' => array(),
                    'expense' => array(),
                    'total_reception' => 0,
                    'total_expense' => 0,
                    'knowing' => isset($employee->name) ? $employee->name : Auth::user()->full_name,
                    'knowing2' => isset($employee2->name) ? $employee2->name : Auth::user()->full_name,
                    'user' => Auth::user()->full_name
                ];

                $balance = Balance::where('division_id', $division_id)->first();
                if($balance) {
                    $data['closing_balance'] = $balance->amount;
                }

                $report = Report::whereNotIn('id', [$report->id])->orderBy('report_date', 'desc')->first();
                if($report) {
                    $report = $report->toArray();
                    $data['opening_balance_date'] = $report['report_date_format'];
                }

                if(isset($param['reception'])) {
                    if(is_array($param['reception'])) {
                        $reception = Reception::whereIn('id', $param['reception'])->get();
                    } else {
                        $reception = Reception::where('id', $param['reception'])->get();
                    }

                    foreach($reception as $value){
                        $data['reception'][] = [
                            'ma_id' => $value->ma_id,
                            'description' => $value->description . ' a/n ' . $value->name_desc . ' pada tanggal ' . $value->reception_date_format,
                            'id' => $value->reception_id,
                            'amount' => $value->amount,
                        ]; 
                        $data['total_reception'] += $this->convertAmount($value->amount, true);
                    }
                }

                if(isset($param['expense'])) {
                    if(is_array($param['expense'])) {
                        $expense = Expense::whereIn('id', $param['expense'])->get();
                    } else {
                        $expense = Expense::where('id', $param['expense'])->get();
                    }

                    foreach($expense as $value){
                        $data['expense'][] = [
                            'reff_no' => $value->reff_no,
                            'reff_date' => $value->reff_date_format,
                            'ma_id' => $value->ma_id,
                            'description' => $value->description . ' a/n ' . $value->name_desc,
                            'id' => $value->expense_id,
                            'amount' => $value->amount,
                            'status' => $value->status,
                        ];
                        $data['total_expense'] += $this->convertAmount($value->amount, true);
                    }
                }
                
                $data['opening_balance'] = $this->convertAmount($this->convertAmount($data['closing_balance'], true) - $data['total_reception'] + $data['total_expense']);
                $data['total_reception'] = $this->convertAmount($this->convertAmount($data['opening_balance'], true) + $data['total_reception']);
                $data['total_expense'] = $this->convertAmount($data['total_expense']);

                $filename = $reports[$reportType['type']] . ' ' . date('d F y') . '.' . $reportType['ext'];

                if($reportType['ext'] == 'xlsx'){
                    $excel = new ExcelWriter($filename, $reportType['type'], config('global.report.header.accountability_fakultas'), $data);
                    $filepath = $excel->write();
                } else {
                    $orientation = 'landscape';
                    $pdf = Pdf::loadView('partials.print.accountability_fakultas', $data);

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

                    $filepath = $pathMonth . '/' . $filename;
                    $pdf->setPaper('a4', $orientation);
                    $pdf->save($filepath);
                }
            } else if($reportType['type'] == config('global.report.code.accountability'))  {
                $data = [
                    'header' => $divisions[$division_id],
                    'balance' => 0,
                    'report_date' => $date->format('d F y'),
                    'expense' => array(),
                    'total_expense' => 0,
                    'knowing' => isset($employee->name) ? $employee->name : Auth::user()->full_name,
                    'knowing2' => isset($employee2->name) ? $employee2->name : Auth::user()->full_name,
                    'user' => Auth::user()->full_name
                ];

                $balance = Balance::where('division_id', $division_id)->first();
                if($balance) {
                    $data['balance'] = $balance->amount;
                }

                if(isset($param['expense'])) {
                    if(is_array($param['expense'])) {
                        $expense = Expense::whereIn('id', $param['expense'])->where('status', config('global.status.code.finished'))->get();
                    } else {
                        $expense = Expense::where('id', $param['expense'])->where('status', config('global.status.code.finished'))->get();
                    }

                    foreach($expense as $value){
                        $data['expense'][] = [
                            'reff_no' => $value->reff_no,
                            'reff_date' => $value->reff_date_format,
                            'ma_id' => $value->ma_id,
                            'description' => $value->description . ' a/n ' . $value->name_desc,
                            'amount' => $value->amount,
                        ];
                        $data['total_expense'] += $this->convertAmount($value->amount, true);
                    }
                }

                $data['total_expense'] = $this->convertAmount($data['total_expense']);

                $filename = $reports[$reportType['type']] . ' ' . date('d F y') . '.' . $reportType['ext'];

                if($reportType['ext'] == 'xlsx'){
                    $excel = new ExcelWriter($filename, $reportType['type'], config('global.report.header.accountability'), $data);
                    $filepath = $excel->write();
                } else {
                    $orientation = 'landscape';
                    $pdf = Pdf::loadView('partials.print.accountability', $data);

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

                    $filepath = $pathMonth . '/' . $filename;
                    $pdf->setPaper('a4', $orientation);
                    $pdf->save($filepath);
                }
            } else {
                $data = [
                    'header' => $divisions[$division_id],
                    'report_date' => $date->format('d F y'),
                    'year' => $report->years,
                    'expense' => array(),
                    'total_expense' => 0,
                    'knowing' => isset($employee->name) ? $employee->name : Auth::user()->full_name,
                ];

                if(isset($param['expense'])) {
                    if(is_array($param['expense'])) {
                        $expense = Expense::whereIn('id', $param['expense'])->where('type', config('global.type.code.white'))->get();
                    } else {
                        $expense = Expense::where('id', $param['expense'])->where('type', config('global.type.code.white'))->get();
                    }

                    foreach($expense as $value){
                        $data['expense'][] = [
                            'reff_date' => $value->reff_date_format,
                            'ma_id' => $value->ma_id,
                            'description' => $value->description,
                            'name' => ' a/n ' . $value->name_desc,
                            'amount' => $value->amount,
                        ];
                        $data['total_expense'] += $this->convertAmount($value->amount, true);
                    }
                }

                $data['total_expense'] = $this->convertAmount($data['total_expense']);


                $filename = $reports[$reportType['type']] . ' ' . date('d F y') . '.' . $reportType['ext'];

                if($reportType['ext'] == 'xlsx'){
                    $excel = new ExcelWriter($filename, $reportType['type'], config('global.report.header.accountability_umd'), $data);
                    $filepath = $excel->write();
                } else {
                    $orientation = 'potrait';
                    $pdf = Pdf::loadView('partials.print.accountability_umd', $data);

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

                    $filepath = $pathMonth . '/' . $filename;
                    $pdf->setPaper('a4', $orientation);
                    $pdf->save($filepath);
                }
            }

            $param = SecureHelper::pack(['path' => $filepath, 'name' => $filename]);

            $response = new Response(true, 'Report successfuly printed', 1);
            $response->setRedirect(route('report.accountability.download', ['id' => $param]));
        } else {
            $response = new Response(false, 'Report printed failed. Please try again');
        }
        return response()->json($response->responseJson());
    }

    public function download($id) {
        $param = SecureHelper::unpack($id);

        if (!is_array($param)) {
            return abort(404);
        }

        $headers = array(
            //'Content-Type: application/pdf',
            'Content-Disposition: attachment;filename=' . $param['name'],
            'Cache-Control: max-age=0',
            'Pragma: no-cache',
            'Expires: 0'
        );

        return response()->download($param['path'], $param['name'], $headers);
    }
}
