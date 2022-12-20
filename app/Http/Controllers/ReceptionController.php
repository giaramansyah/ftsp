<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Expense;
use App\Models\HistoryBalance;
use App\Models\MapData;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
        $reception_id = $this->generate();

        $view = ['yearArr' => $years, 'divisionArr' => $divisions, 'reception_id' => $reception_id, 'action' => route('transaction.reception.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'mandatory' => $this->hasPrivilege($this->_create)];

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
        if ($data['data_id']) {
            $from_ma = true;
            $data['data'] = Data::find($data['data_id'])->toArray();
            $map = MapData::where('data_id', $data['data_id'])->get()->toArray();
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

        $view = ['staffArr' => $staffArr, 'action' => route('transaction.reception.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'from_ma' => $from_ma, 'mandatory' => $this->hasPrivilege($this->_update)];

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
            $reception = Reception::select(['id', 'reception_id', 'ma_id', 'reception_date', 'amount', 'updated_at'])->where('year', $year)->where('division_id', $division)->orderBY('updated_at', 'desc');
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

            $reception = Expense::select(['data_id'])->where('type', config('global.type.code.white'))->get()->toArray();
            $reception = array_column($reception, 'data_id');
            $data = Data::select(['id', 'ma_id', 'description', 'amount'])->whereIn('id', $reception)->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('input');

            $table->addColumn('input', function ($row) {
                $expense = Expense::where('type', config('global.type.code.white'))->where('data_id', $row->id)->first();
                $column = '<div class="form-check">
                <input class="form-check-input" type="radio" name="ma" id="ma' . $row->ma_id . '" value="' . SecureHelper::secure($row->id) . '" data-ma="' . $row->ma_id . '" data-desc="' . $expense->description . '" data-subdesc="' . $expense->sub_description . '" data-name="' . $expense->name . '" data-pic="' . $expense->staff_id . '" data-amount="' . $expense->amount . '" data-textamount="' . $expense->text_amount . '">
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
        if ($data['data_id']) {
            $from_ma = true;
            $data['data'] = Data::find($data['data_id'])->toArray();
        }

        $view = ['from_ma' => $from_ma, 'is_update' => $this->hasPrivilege($this->_update)];

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
                    'data_id' => isset($param['from_ma_id']) && $param['from_ma_id'] == 1 ? SecureHelper::unsecure($param['data_id']) : null,
                    'ma_id' => isset($param['from_ma_id']) && $param['from_ma_id'] == 1 ? $param['ma_id'] : null,
                    'name' => $param['name'],
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
                $reception->ma_id = isset($param['data_id']) ? $param['ma_id'] : null;
                $reception->staff_id = isset($param['data_id']) ? $param['staff_id'] : null;
                $reception->name = $param['name'];
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

    private function generate()
    {
        $prefix = 'I';
        $year = date('y');
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

        return $prefix . $year . $number;
    }
}
