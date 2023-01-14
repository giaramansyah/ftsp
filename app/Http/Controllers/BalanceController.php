<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\HistoryBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BalanceController extends Controller
{
    protected $_create = 'BACR';
    protected $_update = 'BAUP';
    protected $_delete = 'BARM';
    protected $_readall = 'BARA';
    protected $_readid = 'BARD';

    public function index()
    {
        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.balance.index', ['is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function add()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $division = $this->getCompactDivisions();

        $view = ['divisionArr' => $division, 'action' => route('master.balance.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.balance.form', $view);
    }

    public function edit($id)
    {
        if (!$this->hasPrivilege($this->_readid)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Balance::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('division_id' => $data['division_id']);
        }

        $transaction = $this->getTransactions();

        $view = ['transactionArr' => $transaction, 'action' => route('master.balance.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'mandatory' => $this->hasPrivilege($this->_readid)];

        return view('contents.balance.form', array_merge($data, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = Balance::select(['id', 'division_id', 'amount', 'updated_at'])->where('is_trash', 0)->orderBy('id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('balance', 'action');
            $table->addIndexColumn();

            $table->addColumn('balance', function ($row) {
                if ($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="' . route('master.balance.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->division . '</a>';
                } else {
                    $column = $row->division;
                }

                return $column;
            });

            if ($this->hasPrivilege($this->_update) || $this->hasPrivilege($this->_delete)) {
                $table->addColumn('action', function ($row) {
                    $column = '';

                    if ($this->hasPrivilege($this->_update)) {
                        $param = array('class' => 'btn-xs', 'action' => route('master.balance.edit', ['id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.edit', $param)->render();
                    }

                    if ($this->hasPrivilege($this->_delete)) {
                        $param = array('class' => 'btn-xs', 'source' => 'table', 'action' => route('master.balance.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.delete', $param)->render();
                    }

                    return $column;
                });
            }

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
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

        $data = Balance::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;
        $data['histories'] = Balance::find($plainId)->histories()->select('created_at', 'description', 'transaction_id', 'amount')->orderBy('created_at', 'desc')->get()->toArray();
        foreach ($data['histories'] as $key => $value) {
            if ($key == 0) {
                $data['histories'][$key]['balance'] = $data['amount'];
            } else {
                if ($data['histories'][($key - 1)]['transaction_id'] == config('global.transaction.code.debet')) {
                    $balance = $this->convertAmount($data['histories'][($key - 1)]['balance'], true) + $this->convertAmount($data['histories'][($key - 1)]['amount'], true);
                    $data['histories'][$key]['balance'] = $this->convertAmount($balance);
                }

                if ($data['histories'][($key - 1)]['transaction_id'] == config('global.transaction.code.credit')) {
                    $balance = $this->convertAmount($data['histories'][($key - 1)]['balance'], true) - $this->convertAmount($data['histories'][($key - 1)]['amount'], true);
                    $data['histories'][$key]['balance'] = $this->convertAmount($balance);
                }
            }
        }

        $this->writeAppLog($this->_readid, 'Balance : ' . $data['division']);

        $view = ['is_update' => $this->hasPrivilege($this->_update), 'is_delete' => $this->hasPrivilege($this->_delete)];

        return view('contents.balance.view', array_merge($data, $view));
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

                $balance = Balance::where('division_id', $param['division_id'])->where('is_trash', 0)->first();
                if (!$balance) {
                    $balance = Balance::create([
                        'division_id' => $param['division_id'],
                        'amount' => $param['amount'],
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);
                    if ($balance->id) {
                        HistoryBalance::create([
                            'balance_id' => $balance->id,
                            'amount' => $param['amount'],
                            'description' => 'Saldo Awal',
                            'transaction_id' => config('global.transaction.code.credit')
                        ]);
                        $response = new Response(true, __('Balance created successfuly'), 1);
                        $response->setRedirect(route('master.balance.index'));
                        $divisions = array_combine(config('global.compact_division.code'), config('global.compact_division.desc'));
                        $this->writeAppLog($this->_create, 'Balance Division : ' . $divisions[$param['division_id']] . ' Rp ' . $param['amount']);
                    } else {
                        $response = new Response(false, __('Balance create failed. Please try again'));
                    }
                } else {
                    $response = new Response(false, __('Division balance is already registered'));
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
                    $balance = Balance::find($plainId);
                    if (isset($param['amount']) && $param['amount'] != '') {
                        if (isset($param['transaction_id']) && $param['transaction_id'] != '') {
                            if ($param['transaction_id'] == config('global.transaction.code.debet')) {
                                $balance->amount = ($this->convertAmount($balance->amount, true) - $this->convertAmount($param['amount'], true));
                            }

                            if ($param['transaction_id'] == config('global.transaction.code.credit')) {
                                $balance->amount = ($this->convertAmount($balance->amount, true) + $this->convertAmount($param['amount'], true));
                            }
                        }
                    }
                    $balance->updated_by = Auth::user()->username;

                    if ($balance->save()) {
                        HistoryBalance::create([
                            'balance_id' => $balance->id,
                            'amount' => $param['amount'],
                            'description' => 'Update manual oleh ' . Auth::user()->username,
                            'transaction_id' => $param['transaction_id']
                        ]);
                        $response = new Response(true, __('Balance updated successfuly'), 1);
                        $response->setRedirect(route('master.balance.index'));
                        $transaction = array_combine(config('global.transaction.code'), config('global.transaction.desc'));
                        $this->writeAppLog($this->_update, 'Balance Division : ' . $param['division'] . ' ' . $transaction[$param['transaction_id']] . ' Rp ' . $param['amount']);
                    } else {
                        $response = new Response(false, __('Balance update failed. Please try again'));
                    }
                } else {
                    $balance = Balance::find($plainId);
                    if ($param['transaction_id'] == config('global.transaction.code.debet')) {
                        $balance->amount = ($this->convertAmount($balance->amount, true) - $this->convertAmount($param['amount'], true));
                    }

                    if ($param['transaction_id'] == config('global.transaction.code.credit')) {
                        $balance->amount = ($this->convertAmount($balance->amount, true) + $this->convertAmount($param['amount'], true));
                    }
                    $balance->updated_by = Auth::user()->username;

                    if ($balance->save()) {
                        HistoryBalance::create([
                            'balance_id' => $balance->id,
                            'amount' => $param['amount'],
                            'description' => 'Update manual oleh ' . Auth::user()->username,
                            'transaction_id' => $param['transaction_id']
                        ]);

                        $response = new Response(true, __('Balance updated successfuly'), 1);
                        $response->setRedirect(route('master.balance.index'));
                        $transaction = array_combine(config('global.transaction.code'), config('global.transaction.desc'));
                        $this->writeAppLog($this->_update, 'Balance Division : ' . $param['division'] . ' ' . $transaction[$param['transaction_id']] . ' Rp ' . $param['amount']);
                    } else {
                        $response = new Response(false, __('Balance update failed. Please try again'));
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

            $balance = Balance::find($plainId);
            $balance->is_trash = 1;

            if ($balance->save()) {
                $response = new Response(true, __('Balance deleted successfuly'), 1);
                $response->setRedirect(route('master.balance.index'));
                $divisions = array_combine(config('global.compact_division.code'), config('global.compact_division.desc'));
                $this->writeAppLog($this->_delete, 'Balance Division : ' . $divisions[$balance->division_id]);
            } else {
                $response = new Response(false, __('Balance delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }
}
