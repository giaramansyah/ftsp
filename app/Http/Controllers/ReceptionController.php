<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HistoryBalance;
use App\Models\MapData;
use App\Models\MapExpense;
use App\Models\Reception;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ReceptionController extends Controller
{
    protected $_create = 'RECR';
    protected $_update = 'REUP';
    protected $_readall = 'RERA';
    protected $_readid = 'RERD';

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

        return view('contents.reception.index', $view);
    }

    public function add()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $years = $this->getYears();
        $divisions = $this->getDivisions();
        $employees = $this->getEmployees();

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'employeeArr' => $employees, 'action' => route('transaction.reception.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.reception.add', $view);
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

        $data = Reception::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $from_ma = false;
        $staffArr = array();
        if ($data['expense_id']) {
            $from_ma = true;

            $data['data'] = Expense::find($data['expense_id'])->toArray();

            $map = MapExpense::where('expense_id', $data['expense_id'])->get()->toArray();
            $data_id = array_column($map, 'data_id');

            $map = MapData::where('data_id', $data_id[0])->get()->toArray();
            foreach ($map as $value) {
                $staffArr[] = array(
                    'id' => $value['staff_id'],
                    'name' => $value['staff'],
                );
            }
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('reception_id' => $data['reception_id']);
        }

        $employees = $this->getEmployees();

        $view = ['staffArr' => $staffArr, 'employeeArr' => $employees, 'action' => route('transaction.reception.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'from_ma' => $from_ma, 'mandatory' => $this->hasPrivilege($this->_update)];

        return view('contents.reception.edit', array_merge($view, $data));
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
            $reception = Reception::select(['id', 'reception_id', 'ma_id', 'reception_date', 'amount', 'updated_at'])->where('year', $year)->where('division_id', $division);
            if (Auth::user()->staff_id != config('global.staff.code.admin')) {
                $reception->where('staff_id', Auth::user()->staff_id);
            }
            $reception->orderBY('updated_at', 'desc');
            $table = DataTables::eloquent($reception);
            $rawColumns = array('reception');
            $table->addIndexColumn();

            $table->addColumn('reception', function ($row) {
                if ($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="' . route('transaction.reception.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->reception_id . '</a>';
                } else {
                    $column = $row->reception_id;
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

            $data = Data::select(['id'])->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->get()->toArray();
            $data = array_column($data, 'id');

            $reception = Reception::select(['expense_id'])->whereNotNull('expense_id')->where('year', $year)->where('division_id', $division_id)->get()->toArray();
            $reception = array_column($reception, 'expense_id');

            $map = MapExpense::whereIn('data_id', $data)->whereNotIn('expense_id', $reception)->get()->toArray();
            $map = array_column($map, 'expense_id');

            $expense = Expense::select(['*'])->where('type', config('global.type.code.white'))->whereIn('id', $map)->with('map');
            $table = DataTables::eloquent($expense);
            $rawColumns = array('input');

            $table->addColumn('input', function ($row) {
                $column = '<div class="form-check">
                <input class="form-check-input" type="radio" name="ma" id="ma' . $row->ma_id . '" value="' . SecureHelper::secure($row->map[0]->data_id) . '" data-id= "' . SecureHelper::secure($row->id) . '" data-ma="' . $row->ma_id . '" data-desc="' . $row->description . '" data-subdesc="' . $row->sub_description . '" data-name="' . $row->name . '" data-pic="' . $row->staff_id . '" data-amount="' . $row->amount . '" data-textamount="' . $row->text_amount . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
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

        $data = Reception::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;

        $from_ma = false;
        if ($data['expense_id']) {
            $from_ma = true;

            $expense = Expense::find($data['expense_id']);

            if ($expense['is_multiple'] == 1) {
                $map = MapExpense::where('expense_id', $data['expense_id'])->get()->toArray();
                $data_id = array_column($map, 'data_id');

                $description = array();
                $amount = 0;
                $data['data'] = Data::where('id', $data_id[0])->first()->toArray();

                $datas = Data::whereIn('id', $data_id)->get();
                foreach ($datas as $value) {
                    $description[] = $value->description;
                    $amount += $this->convertAmount($value->amount, true);
                }

                $data['data']['description'] = implode('<br>', $description);
                $data['data']['amount'] = $this->convertAmount($amount);
            } else {
                $map = MapExpense::where('expense_id', $data['expense_id'])->first();
                $data['data'] = Data::where('id', $map->data_id)->first()->toArray();
            }
        }

        $employeeArr = $this->getEmployees();

        $view = ['employeeArr' => $employeeArr, 'from_ma' => $from_ma, 'is_update' => $this->hasPrivilege($this->_update)];

        $this->writeAppLog($this->_readid, 'Reception : ' . $data['reception_id']);

        return view('contents.reception.view', array_merge($data, $view));
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

                $reception = Reception::create([
                    'reception_id' => $param['reception_id'],
                    'reception_date' => $param['reception_date'],
                    'year' => SecureHelper::unsecure($param['year']),
                    'division_id' => SecureHelper::unsecure($param['division_id']),
                    'description' => $param['description'],
                    'sub_description' => $param['sub_description'],
                    'expense_id' => isset($param['from_ma_id']) && $param['from_ma_id'] == 1 ? SecureHelper::unsecure($param['expense_id']) : null,
                    'ma_id' => isset($param['from_ma_id']) && $param['from_ma_id'] == 1 ? $param['ma_id'] : null,
                    'name' => isset($param['name']) && $param['name'] != '' ? $param['name'] : 0,
                    'staff_id' => isset($param['from_ma_id']) && $param['from_ma_id'] == 1 ? $param['staff_id'] : null,
                    'amount' => $param['amount'],
                    'text_amount' => $param['text_amount'],
                    'created_by' => Auth::user()->username,
                    'updated_by' => Auth::user()->username,
                ]);

                if ($reception->id) {
                    $division_id = SecureHelper::unsecure($param['division_id']);
                    $balance = Balance::where('division_id', $division_id)->where('is_trash', 0)->first();
                    if ($balance) {
                        $balance->amount = ($this->convertAmount($balance->amount, true) + $this->convertAmount($param['amount'], true));
                        $balance->updated_by = Auth::user()->username;
                        if ($balance->save()) {
                            HistoryBalance::create([
                                'balance_id' => $balance->id,
                                'amount' => $param['amount'],
                                'description' => $param['description'],
                                'data_id' => $reception->id,
                                'transaction_id' => config('global.transaction.code.credit')
                            ]);
                        }
                    }

                    $response = new Response(true, __('Reception created successfuly'), 1);
                    $response->setRedirect(route('transaction.reception.view', ['id' => SecureHelper::secure($reception->id)]));

                    $this->writeAppLog($this->_create, 'Reception : ' . $param['reception_id']);
                } else {
                    $response = new Response(false, __('Reception create failed. Please try again'));
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

                $reception = Reception::find($plainId);
                $reception->reception_date = $param['reception_date'];
                $reception->description = $param['description'];
                $reception->sub_description = $param['sub_description'];
                $reception->ma_id = isset($param['expense_id']) ? $param['ma_id'] : null;
                $reception->staff_id = isset($param['expense_id']) ? $param['staff_id'] : null;
                $reception->name = isset($param['name']) && $param['name'] != '' ? $param['name'] : 0;
                $reception->amount = $param['amount'];
                $reception->text_amount = $param['text_amount'];
                $reception->created_by = Auth::user()->username;

                if ($reception->save()) {
                    $division_id = $param['division_id'];
                    $balance = Balance::where('division_id', $division_id)->where('is_trash', 0)->first();
                    if ($balance) {
                        $history = HistoryBalance::where('balance_id', $balance->id)->where('data_id', $reception->id)->where('transaction_id', config('global.transaction.code.credit'))->first();
                        $balance->amount = ($this->convertAmount($balance->amount, true) - $this->convertAmount($history->amount, true) + $this->convertAmount($param['amount'], true));
                        $balance->updated_by = Auth::user()->username;
                        if ($balance->save()) {
                            $history->amount = $param['amount'];
                            $history->save();
                        }
                    }

                    $response = new Response(true, __('Reception updated successfuly'), 1);
                    $response->setRedirect(route('transaction.reception.view', ['id' => SecureHelper::secure($reception->id)]));

                    $this->writeAppLog($this->_create, 'Reception : ' . $param['reception_id']);
                } else {
                    $response = new Response(false, __('Reception updated failed. Please try again'));
                }
            }
        }

        return response()->json($response->responseJson());
    }

    public function print(Request $request, $id)
    {
        if (!$this->hasPrivilege($this->_update)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $type = $param['type'];

        if ($type != config('global.type.code.green')) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $data = Reception::find($plainId)->toArray();

        $filename = date('d_M_Y_H_i_s') . '_' . config('global.type.desc.green') . '_' . $data['reception_id'] . '.pdf';

        $data['knowing'] = Employee::find($param['knowing'])->name;
        $data['approver'] = Employee::find($param['approver'])->name;
        $data['sender'] = Employee::find($param['sender'])->name;
        $data['reciever'] = Employee::find($param['reciever'])->name;
        $pdf = Pdf::loadView('partials.print.green', $data);

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

        $pdf->setPaper('a4', 'portrait');
        $pdf->save($pathMonth . '/' . $filename);

        $id = SecureHelper::pack(['file' => $filename, 'path' => public_path('download')]);

        $response = new Response(true, 'Report successfuly printed', 1);
        $response->setRedirect(route('transaction.reception.download', ['id' => $id]));

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

        if($file->status) {
            $headers = array(
                'Content-Type: application/pdf',
                'Content-Disposition: attachment;filename=' . $file->name,
                'Cache-Control: max-age=0',
                'Pragma: no-cache',
                'Expires: 0'
            );
    
            return response()->download($file->path, $file->name, $headers); 
        } else {
            abort(404);
        }
    }

    public function generate(Request $request)
    {
        if (($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $year = SecureHelper::unsecure($request->input('year'));
        if (!$year) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $division_id = SecureHelper::unsecure($request->input('division_id'));
        if (!$division_id) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $prefix = '';
        $year = substr(strval($year), 2, 2);

        switch ($division_id) {
            case config('global.division.code.fakultas'):
            case config('global.division.code.arsitektur'):
            case config('global.division.code.sipil'):
                $prefix = 'F';
                break;
            case config('global.division.code.mta'):
                $prefix = 'MTA';
                break;
            case config('global.division.code.mts'):
                $prefix = 'MTS';
                break;
            default:
                $response = new Response();
                return response()->json($response->responseJson());
                break;
        }

        $number = '0001';

        $reception = Reception::select('reception_id')->where('reception_id', 'like', '%' . $prefix . $year . '%')->orderBy('reception_id', 'desc')->first();
        if ($reception) {
            $currentNumber = Str::after($reception->reception_id, $prefix . $year);
            $currentNumber++;
            $currentNumber = strval($currentNumber);
            $temp = '';
            for ($i = 0; $i < 4 - strlen($currentNumber); $i++) {
                $temp .= '0';
            }

            $number = $temp . $currentNumber;
        }

        $response = new Response(true, 'Success', 1);
        $response->setData($prefix . $year . $number);
        return response()->json($response->responseJson());
    }
}
