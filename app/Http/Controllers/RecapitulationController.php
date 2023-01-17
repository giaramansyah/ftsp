<?php

namespace App\Http\Controllers;

use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\Expense;
use App\Models\MapExpense;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RecapitulationController extends Controller
{
    protected $_readall = 'RCRA';

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
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));

        $view = ['yearArr' =>  $years, 'year' => $currYear, 'yearDesc' => $yearsDesc[$currYear]];

        return view('contents.recapitulation.index', $view);
    }

    public function division($id)
    {
        $param = SecureHelper::unpack($id);

        if (!is_array($param)) {
            return abort(404);
        }

        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $ma = Data::select('id', 'amount')->where('is_trash', 0)->where('division_id', $param['division_id'])->get()->toArray();
        $data_id = array_column($ma, 'id');

        $amount = 0;
        foreach ($ma as $row) {
            $amount += $this->convertAmount($row['amount'], true);
        }

        $used = 0;
        $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
        if ($expense) {
            $used += $expense->used;
        } else {
            $used += '0';
        }

        $data = [
            'amount' => $this->convertAmount($amount),
            'used' => $this->convertAmount($used),
            'percent' => round(($used / $amount) * 100, 2) . '%',
            'remain' => $this->convertAmount($amount - $used),
        ];

        $years = $this->getYears();
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));
        $divisionDesc = array_combine(config('global.division.code'), config('global.division.desc'));

        $view = ['year' => $param['year'], 'division_id' => $param['division_id'], 'yearDesc' => $yearsDesc[$param['year']], 'divisionDesc' => $divisionDesc[$param['division_id']]];

        return view('contents.recapitulation.division', array_merge($view, $data));
    }

    public function data($id)
    {
        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $data = Data::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;

        $expense =  MapExpense::selectRaw('sum(amount) as amount')->where('data_id', $plainId)->first()->toArray();
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

        $view = ['year' => $data['year'], 'division_id' => $data['division_id']];

        return view('contents.recapitulation.data', array_merge($view, $data));
    }

    public function getListDivision(Request $request)
    {
        if ($request->ajax()) {
            $param = $request->input('id');

            if (!isset($param)) {
                $year = 0;
            } else {
                $param = SecureHelper::unsecure($param);

                if (!$param) {
                    $year = 0;
                } else {
                    $year = $param;
                }
            }
        }

        $data = Data::selectRaw('division_id, year, sum(amount) as amount')->where('is_trash', 0)->where('year', $year)->orderBy('division_id')->groupBy('division_id');
        $table = DataTables::eloquent($data);
        $rawColumns = array('division_link', 'used', 'remain', 'percent');

        $table->addColumn('division_link', function ($row) {
            return '<a href="' . route('report.recapitulation.division', ['id' => SecureHelper::pack(['year' => $row->year, 'division_id' => $row->division_id])]) . '">' . $row->division . '</a>';
        });

        $table->addColumn('used', function ($row) {
            $ma = Data::select('id')->where('is_trash', 0)->where('division_id', $row->division_id)->get()->toArray();
            $data_id = array_column($ma, 'id');
            $expense =  MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $column = $this->convertAmount($expense->used);
            } else {
                $column = '0';
            }

            return $column;
        });

        $table->addColumn('remain', function ($row) {
            $ma = Data::select('id')->where('is_trash', 0)->where('division_id', $row->division_id)->get()->toArray();
            $data_id = array_column($ma, 'id');
            $expense =  MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used = $this->convertAmount($expense->used, true);
                $amount = $this->convertAmount($row->amount, true);
                $column = $this->convertAmount($amount - $used);
            } else {
                $column = $row->amount;
            }

            return $column;
        });

        $table->addColumn('percent', function ($row) {
            $ma = Data::select('id')->where('is_trash', 0)->where('division_id', $row->division_id)->get()->toArray();
            $data_id = array_column($ma, 'id');
            $expense =  MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used = $this->convertAmount($expense->used, true);
                $amount = $this->convertAmount($row->amount, true);
                $column = round(($used / $amount) * 100, 2);
            } else {
                $column = '0.00';
            }

            return $column . '%';
        });

        $table->rawColumns($rawColumns);

        $this->writeAppLog($this->_readall);

        $json = $table->toArray();

        if (!empty($json['data'])) {
            $mid = array(
                'amount' => 0,
                'division' => "ftsp",
                'division_link' => "ftsp",
                'division_id' => 99,
                'percent' => 0,
                'remain' => 0,
                'staff' => "",
                'staff_id' => [],
                'used' => 0,
                'years' => null,
            );

            $space = array(
                'amount' => '',
                'division' => '',
                'division_link' => '',
                'division_id' => '',
                'percent' => '',
                'remain' => '',
                'staff' => '',
                'staff_id' => [],
                'used' => '',
                'years' => null,
            );

            $amount = 0;
            $used = 0;
            foreach ($json['data'] as $row) {
                if (in_array($row['division_id'], [1, 2, 3])) {
                    $amount += $this->convertAmount($row['amount'], true);
                    $used += $this->convertAmount($row['used'], true);
                }
            }

            $mid['amount'] = $this->convertAmount($amount);
            $mid['used'] = $this->convertAmount($used);
            $mid['remain'] = $this->convertAmount($amount - $used);
            if ($amount > 0) {
                $mid['percent'] = round(($used / $amount) * 100, 2) . '%';
            } else {
                $mid['percent'] = '0%';
            }

            array_splice($json['data'], 3, 0, array($mid));
            array_splice($json['data'], 4, 0, array($space));
        }

        return json_encode($json);
    }

    public function getListPic(Request $request)
    {
        if ($request->ajax()) {
            $param = $request->input('id');

            if (!isset($param)) {
                $year = 0;
            } else {
                $param = SecureHelper::unsecure($param);

                if (!$param) {
                    $year = 0;
                } else {
                    $year = $param;
                }
            }
        }

        $data = collect();

        $dekan = Data::select('division_id', 'id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.dekan'))->get()->toArray();
        $data_id = array_column($dekan, 'id');

        if (!empty($data_id)) {
            $amount = 0;
            foreach ($dekan as $row) {
                $amount += $this->convertAmount($row['amount'], true);
            }

            $used = 0;
            $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used += $expense->used;
            } else {
                $used += '0';
            }

            if ($amount > 0) {
                $percent = round(($used / $amount) * 100, 2) . '%';
            } else {
                $percent = '0%';
            }

            $dekanData = [
                'pic' => config('global.staff.desc.dekan'),
                'amount' => $this->convertAmount($amount),
                'used' => $this->convertAmount($used),
                'percent' => $percent,
                'remain' => $this->convertAmount($amount - $used),
            ];

            $data->push($dekanData);
        }

        $wd1 = Data::select('division_id', 'id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd1'))->get()->toArray();
        $data_id = array_column($wd1, 'id');

        if (!empty($data_id)) {
            $amount = 0;
            foreach ($wd1 as $row) {
                $amount += $this->convertAmount($row['amount'], true);
            }

            $used = 0;
            $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used += $expense->used;
            } else {
                $used += '0';
            }

            if ($amount > 0) {
                $percent = round(($used / $amount) * 100, 2) . '%';
            } else {
                $percent = '0%';
            }

            $wd1Data = [
                'pic' => config('global.staff.desc.wd1'),
                'amount' => $this->convertAmount($amount),
                'used' => $this->convertAmount($used),
                'percent' => $percent,
                'remain' => $this->convertAmount($amount - $used),
            ];

            $data->push($wd1Data);
        }

        $wd2 = Data::select('division_id', 'id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd2'))->has('staffs', '=', 1)->get()->toArray();
        $data_id = array_column($wd2, 'id');

        if (!empty($data_id)) {
            $amount = 0;
            foreach ($wd2 as $row) {
                $amount += $this->convertAmount($row['amount'], true);
            }

            $used = 0;
            $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used += $expense->used;
            } else {
                $used += '0';
            }

            if ($amount > 0) {
                $percent = round(($used / $amount) * 100, 2) . '%';
            } else {
                $percent = '0%';
            }

            $wd2Data = [
                'pic' => config('global.staff.desc.wd2'),
                'amount' => $this->convertAmount($amount),
                'used' => $this->convertAmount($used),
                'percent' => $percent,
                'remain' => $this->convertAmount($amount - $used),
            ];

            $data->push($wd2Data);
        }

        $wd3 = Data::select('division_id', 'id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd3'))->get()->toArray();
        $data_id = array_column($wd3, 'id');

        if (!empty($data_id)) {
            $amount = 0;
            foreach ($wd3 as $row) {
                $amount += $this->convertAmount($row['amount'], true);
            }

            $used = 0;
            $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used += $expense->used;
            } else {
                $used += '0';
            }

            if ($amount > 0) {
                $percent = round(($used / $amount) * 100, 2) . '%';
            } else {
                $percent = '0%';
            }

            $wd3Data = [
                'pic' => config('global.staff.desc.wd3'),
                'amount' => $this->convertAmount($amount),
                'used' => $this->convertAmount($used),
                'percent' => $percent,
                'remain' => $this->convertAmount($amount - $used),
            ];

            $data->push($wd3Data);
        }

        $wd4 = Data::select('division_id', 'id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd4'))->get()->toArray();
        $data_id = array_column($wd4, 'id');

        if (!empty($data_id)) {
            $amount = 0;
            foreach ($wd4 as $row) {
                $amount += $this->convertAmount($row['amount'], true);
            }

            $used = 0;
            $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
            if ($expense) {
                $used += $expense->used;
            } else {
                $used += '0';
            }

            if ($amount > 0) {
                $percent = round(($used / $amount) * 100, 2) . '%';
            } else {
                $percent = '0%';
            }

            $wd4Data = [
                'pic' => config('global.staff.desc.wd4'),
                'amount' => $this->convertAmount($amount),
                'used' => $this->convertAmount($used),
                'percent' => $percent,
                'remain' => $this->convertAmount($amount - $used),
            ];

            $data->push($wd4Data);
        }

        $table = DataTables::of($data);

        $this->writeAppLog($this->_readall);

        return $table->toJson();
    }

    public function getDetailDivision(Request $request)
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
                    $division = $param['division_id'];
                }
            }

            $data = Data::select(['id', 'ma_id', 'description', 'amount', 'updated_at'])->where('is_trash', 0)->where('year', $year)->where('division_id', $division)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('description_link', 'used', 'remain', 'percent');
            $table->addIndexColumn();

            $table->addColumn('description_link', function ($row) {
                return '<a href="' . route('report.recapitulation.data', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->description . '</a>';
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

    public function getDetailData(Request $request)
    {
        if ($request->ajax()) {
            $param = $request->input('id');

            if (!isset($param)) {
                $data_id = 0;
            } else {
                $param = SecureHelper::unsecure($param);

                if (!$param) {
                    $data_id = 0;
                } else {
                    $data_id = $param;
                }
            }

            $map = MapExpense::where('data_id', $data_id)->get()->toArray();
            $map = array_column($map, 'expense_id');

            $data =  Expense::whereIn('id', $map)->with('map')->get();

            $collect = collect();
            foreach ($data as $value) {
                $collection = [
                    'reff_no' => $value->reff_no,
                    'description' => $value->description,
                    'reff_date_format' => $value->reff_date_format,
                    'amount' => 0
                ];

                foreach ($value->map as $val) {
                    if ($val['data_id'] == $data_id) {
                        $collection['amount'] = $this->convertAmount($val['amount']);
                    }
                }

                $collect->push($collection);
            }

            $table = DataTables::of($collect);
            $table->addIndexColumn();

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }
}
