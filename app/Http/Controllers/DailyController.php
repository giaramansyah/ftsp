<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class DailyController extends Controller
{
    protected $_create = 'DLCR';

    public function index()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $yearArr = $this->getYears();
        $divisionArr = $this->getCompactDivisions();
        $employeeArr = $this->getEmployees();

        $view = ['yearArr' => $yearArr, 'divisionArr' => $divisionArr, 'employeeArr' => $employeeArr, 'mandatory' => $this->hasPrivilege($this->_create), 'action' => route('report.daily.post')];

        return view('contents.daily.index', $view);
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
        if (!$division_id) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $year = SecureHelper::unsecure($param['year']);
        if (!$year) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $ext =  SecureHelper::unsecure($param['ext']);
        if (!$ext || !in_array($ext, ['xlsx', 'pdf'])) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $reports = array_combine(config('global.report.code'), config('global.report.desc'));
        $divisions = array_combine(config('global.compact_division.code'), config('global.compact_division.report'));
        $date = Carbon::createFromFormat('Y-m-d', $param['daily_date']);

        $filename = $reports[config('global.report.code.daily')] . ' ' . date('d F y') . '.' . $ext;

        $data = [
            'header' => $divisions[$division_id],
            'report_date' => $date->format('d F y'),
            'expense' => array(),
        ];


        if ($division_id != 99) {
            $ma = Data::where('division_id', $division_id)->get()->toArray();
        } else {
            $ma = Data::whereIn('division_id', [config('global.division.code.fakultas'), config('global.division.code.arsitektur'), config('global.division.code.sipil')])->get()->toArray();
        }

        $ma = array_column($ma, 'id');

        $expense = Expense::where('expense_date', $param['daily_date'])->whereIn('data_id', $ma)->get();

        foreach ($expense as $value) {
            $data['expense'][] = [
                'expense_date' => $value->expense_date_format,
                'description' => ($value->type == config('global.type.code.white') ? 'UMD/ ' : '') . $value->description,
                'amount' => $value->amount,
                'account' => $value->account,
                'name' => 'a/n. ' . $value->name_desc,
            ];
        }

        if ($ext == 'xlsx') {
            $response = new Response(false, 'Not Supported yet');
            return response()->json($response->responseJson());
        } else {
            $orientation = 'potrait';
            $pdf = Pdf::loadView('partials.print.daily', $data);

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

        $param = SecureHelper::pack(['path' => $filepath, 'name' => $filename]);

        $response = new Response(true, 'Report successfuly printed', 1);
        $response->setRedirect(route('report.daily.download', ['id' => $param]));
        
        return response()->json($response->responseJson());
    }

    public function download($id)
    {
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
