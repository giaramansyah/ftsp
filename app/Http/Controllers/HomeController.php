<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Balance;
use App\Models\Data;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\MapExpense;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    protected $_year;

    function __construct()
    {
        // $this->_year = 2022;
        $this->_year = date('Y');
    }

    public function index()
    {
        $data = $this->convertAmount(Data::where('year', $this->_year)->sum('amount'));
        $balance = Balance::select('division_id', 'amount')->get();

        $years = $this->getYears();
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));

        if (!isset($yearsDesc[$this->_year])) {
            $yearsDesc[$this->_year] = $this->_year . '-' . ($this->_year + 1);
        }

        $result = array(
            array(
                'title' => 'Total',
                'label' => 'Mata Anggaran ' . $yearsDesc[$this->_year],
                'value' => $data,
                'class' => 'bg-success',
                'prefix' => 'Rp',
                'icon' => 'fas fa-money-bill',
                'is_prepend' => true,
                'is_append' => false,
            ),
        );

        foreach ($balance as $value) {
            $result[] = array(
                'title' => 'Saldo',
                'label' => $value->division,
                'value' => $value->amount,
                'class' => 'bg-info',
                'prefix' => 'Rp',
                'icon' => 'fas fa-dollar',
                'is_prepend' => true,
                'is_append' => false,
            );
        }

        $is_note = false;
        $is_general = false;
        if (Auth::user()->id == 1) {
            $is_note = true;
            $is_general = true;
        } else {
            if (Auth::user()->privilege == ['NTCR', 'NTUP', 'NTRM', 'NTRA', 'NTRD']) {
                $is_note = true;
            } else {
                $is_general = true;
            }
        }

        $years = $this->getYears();

        $view = ['result' => $result, 'is_general' => $is_general, 'is_note' => $is_note, 'yearArr' =>  $years];

        return view('contents.home.index', $view);
    }

    public function getRealization()
    {
        $result = array();
        $result['fakultas'] = $this->fakultas();
        $result['mta'] = $this->mta();
        $result['mts'] = $this->mts();

        $response = new Response(true, 'Success', 1);
        $response->setData($result);

        return response()->json($response->responseJson());
    }

    public function getPending(Request $request)
    {
        if ($request->ajax()) {
            $data = Data::select(['id'])->where('is_trash', 0)->where('year', $this->_year)->orderBy('id')->get()->toArray();
            $data = array_column($data, 'id');

            $map = MapExpense::whereIn('data_id', $data)->groupBy('expense_id')->get()->toArray();
            $map = array_column($map, 'expense_id');

            $expense = Expense::select(['id', 'expense_id', 'ma_id', 'expense_date', 'reff_no', 'reff_date', 'staff_id', 'amount', 'type', 'updated_at', 'status'])->whereIn('id', $map)->where('type', config('global.type.code.white'))->where('status', config('global.status.code.unfinished'))->orderBY('updated_at', 'asc');
            $table = DataTables::eloquent($expense);
            $rawColumns = array('expense');
            $table->addIndexColumn();

            $table->addColumn('expense', function ($row) {
                if ($this->hasPrivilege('EXRD')) {
                    $column = '<a href="' . route('transaction.expense.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->expense_id . '</a>';
                } else {
                    $column = $row->expense_id;
                }

                return $column;
            });

            $table->rawColumns($rawColumns);

            return $table->toJson();
        }
    }

    public function getNote(Request $request)
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

        $years = $this->getYears();
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));

        $note_finished = Note::where('is_trash', 0)->where('year', $year)->where('status', config('global.status.code.finished'))->count('id');
        $note_unfinished = Note::where('is_trash', 0)->where('year', $year)->where('status', config('global.status.code.unfinished'))->count('id');

        $result = array(
            'series' => array(
                'Dekan/Univ',
                'WD 1',
                'WD 2',
                'WD 3',
                'WD 4',
                'Tek. Sipil',
                'Arsitektur',
                'MTS',
                'MTA',
            ),
            'requested' => array(0, 0, 0, 0, 0, 0, 0, 0, 0),
            'approved' => array(0, 0, 0, 0, 0, 0, 0, 0, 0),
            'percentage' => array(0, 0, 0, 0, 0, 0, 0, 0, 0),
            'year' => $yearsDesc[$year],
            'status' => array(
                'finished' => array(
                    'title' => 'Total Status',
                    'label' => 'Surat Selesai ' . $yearsDesc[$year],
                    'value' => $note_finished,
                    'class' => 'bg-success',
                    'prefix' => 'Surat',
                    'icon' => 'fas fa-check',
                    'is_prepend' => false,
                    'is_append' => true,
                ),
                'unfinished' => array(
                    'title' => 'Total Status',
                    'label' => 'Surat Belum Selesai ' . $yearsDesc[$year],
                    'value' => $note_unfinished,
                    'class' => 'bg-danger',
                    'prefix' => 'Surat',
                    'icon' => 'fas fa-rotate',
                    'is_prepend' => false,
                    'is_append' => true,
                ),
            )
        );
        
        //dekan
        $dekan = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.dekan'))->get()->toArray();
        $data_id = array_column($dekan, 'id');

        if (!empty($data_id)) {
            $result['requested'][0] = 0;
            $result['approved'][0] = 0;
            foreach ($dekan as $row) {
                $result['requested'][0] += $this->convertAmount($row['amount'], true);
                $result['approved'][0] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][0] = round(($result['approved'][0]/$result['requested'][0])*100, 2);
        }

        //wd1
        $wd1 = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd1'))->get()->toArray();
        $data_id = array_column($wd1, 'id');

        if (!empty($data_id)) {
            $result['requested'][1] = 0;
            $result['approved'][1] = 0;
            foreach ($wd1 as $row) {
                $result['requested'][1] += $this->convertAmount($row['amount'], true);
                $result['approved'][1] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][1] = round($result['approved'][1]/$result['requested'][1]*100, 2);
        }

        //wd2
        $wd2 = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd2'))->has('staffs', '=', 1)->get()->toArray();
        $data_id = array_column($wd2, 'id');

        if (!empty($data_id)) {
            $result['requested'][2] = 0;
            $result['approved'][2] = 0;
            foreach ($wd2 as $row) {
                $result['requested'][2] += $this->convertAmount($row['amount'], true);
                $result['approved'][2] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][2] = round($result['approved'][2]/$result['requested'][2]*100, 2);
        }

        //wd3
        $wd3 = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd3'))->get()->toArray();
        $data_id = array_column($wd3, 'id');

        if (!empty($data_id)) {
            $result['requested'][3] = 0;
            $result['approved'][3] = 0;
            foreach ($wd3 as $row) {
                $result['requested'][3] += $this->convertAmount($row['amount'], true);
                $result['approved'][3] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][3] = round($result['approved'][3]/$result['requested'][3]*100, 2);
        }

        //wd4
        $wd4 = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.fakultas'))->whereRelation('staffs', 'staff_id', config('global.staff.code.wd4'))->get()->toArray();
        $data_id = array_column($wd4, 'id');

        if (!empty($data_id)) {
            $result['requested'][4] = 0;
            $result['approved'][4] = 0;
            foreach ($wd4 as $row) {
                $result['requested'][4] += $this->convertAmount($row['amount'], true);
                $result['approved'][4] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][4] = round($result['approved'][4]/$result['requested'][4]*100, 2);
        }

        //sipil
        $sipil = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.sipil'))->whereRelation('staffs', 'staff_id', config('global.staff.code.kaprodis1'))->get()->toArray();
        $data_id = array_column($sipil, 'id');

        if (!empty($data_id)) {
            $result['requested'][5] = 0;
            $result['approved'][5] = 0;
            foreach ($sipil as $row) {
                $result['requested'][5] += $this->convertAmount($row['amount'], true);
                $result['approved'][5] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][5] = round($result['approved'][5]/$result['requested'][5]*100, 2);
        }
        
        //arsitek
        $arsitek = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.arsitektur'))->whereRelation('staffs', 'staff_id', config('global.staff.code.kaprodis1'))->get()->toArray();
        $data_id = array_column($arsitek, 'id');

        if (!empty($data_id)) {
            $result['requested'][6] = 0;
            $result['approved'][6] = 0;
            foreach ($arsitek as $row) {
                $result['requested'][6] += $this->convertAmount($row['amount'], true);
                $result['approved'][6] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][6] = round($result['approved'][6]/$result['requested'][6]*100, 2);
        }

        //mts
        $mts = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.mts'))->whereRelation('staffs', 'staff_id', config('global.staff.code.kaprodis2'))->get()->toArray();
        $data_id = array_column($mts, 'id');

        if (!empty($data_id)) {
            $result['requested'][7] = 0;
            $result['approved'][7] = 0;
            foreach ($mts as $row) {
                $result['requested'][7] += $this->convertAmount($row['amount'], true);
                $result['approved'][7] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][7] = round($result['approved'][7]/$result['requested'][7]*100, 2);
        }
        
        //mta
        $mta = Note::select('id', 'amount', 'amount_requested')->where('is_trash', 0)->where('year', $year)->where('division_id', config('global.division.code.mta'))->whereRelation('staffs', 'staff_id', config('global.staff.code.kaprodis2'))->get()->toArray();
        $data_id = array_column($mta, 'id');

        if (!empty($data_id)) {
            $result['requested'][8] = 0;
            $result['approved'][8] = 0;
            foreach ($mta as $row) {
                $result['requested'][8] += $this->convertAmount($row['amount'], true);
                $result['approved'][8] += $this->convertAmount($row['amount_requested'], true);
            }
            $result['percentage'][8] = round($result['approved'][8]/$result['requested'][8]*100, 2);
        }

        $response = new Response(true, 'Success', 1);
        $response->setData($result);

        return response()->json($response->responseJson());
    }

    private function fakultas()
    {
        $budget = Data::whereIn('division_id', [config('global.division.code.fakultas'), config('global.division.code.arsitektur'), config('global.division.code.sipil')])->where('is_trash', 0)->where('year', $this->_year)->sum('amount');

        $data = Data::select('id')->where('is_trash', 0)->whereIn('division_id', [config('global.division.code.fakultas'), config('global.division.code.arsitektur'), config('global.division.code.sipil')])->where('year', $this->_year)->get()->toArray();
        $data_id = array_column($data, 'id');

        $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
        $expense = $this->convertAmount($expense->used, true);

        return array(
            'title' => 'REALISASI ANGGARAN ' . config('global.division.report.fakultas'),
            'series' => array('Mata Anggaran', 'Realisasi'),
            'pagu' => array(
                'color' => '#007bff',
                'value' => ($budget - $expense),
            ),
            'real' => array(
                'color' => '#007bff',
                'value' => $expense,
            ),
            'legend' => array(
                array(
                    'text' => 'Mata Anggaran',
                    'value' => $this->convertAmount($budget),
                    'color' => 'text-primary'
                ),
                array(
                    'text' => 'Realisasi',
                    'value' => $this->convertAmount($expense),
                    'percent' => $budget > 0 ? round(($expense / $budget * 100), 2) : 0.00,
                    'color' => 'text-danger'
                ),
                array(
                    'text' => 'Sisa',
                    'value' => $this->convertAmount($budget - $expense),
                    'percent' => $budget > 0 ? round((($budget - $expense) / $budget * 100), 2) : 0.00,
                    'color' => 'text-gray'
                ),
            ),
        );
    }

    private function mta()
    {
        $budget = Data::where('division_id', config('global.division.code.mta'))->where('is_trash', 0)->where('year', $this->_year)->sum('amount');

        $data = Data::select('id')->where('is_trash', 0)->where('division_id', config('global.division.code.mta'))->where('year', $this->_year)->get()->toArray();
        $data_id = array_column($data, 'id');

        $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
        $expense = $this->convertAmount($expense->used, true);

        return array(
            'title' => 'REALISASI ANGGARAN ' . config('global.division.report.mta'),
            'series' => array('Mata Anggaran', 'Realisasi'),
            'pagu' => array(
                'color' => '#007bff',
                'value' => ($budget - $expense),
            ),
            'real' => array(
                'color' => '#007bff',
                'value' => $expense,
            ),
            'legend' => array(
                array(
                    'text' => 'Mata Anggaran',
                    'value' => $this->convertAmount($budget),
                    'color' => 'text-primary'
                ),
                array(
                    'text' => 'Realisasi',
                    'value' => $this->convertAmount($expense),
                    'percent' => $budget > 0 ? round(($expense / $budget * 100), 2) : 0.00,
                    'color' => 'text-danger'
                ),
                array(
                    'text' => 'Sisa',
                    'value' => $this->convertAmount($budget - $expense),
                    'percent' => $budget > 0 ? round((($budget - $expense) / $budget * 100), 2) : 0.00,
                    'color' => 'text-gray'
                ),
            ),
        );
    }

    private function mts()
    {
        $budget = Data::where('division_id', config('global.division.code.mts'))->where('is_trash', 0)->where('year', $this->_year)->sum('amount');

        $data = Data::select('id')->where('is_trash', 0)->where('division_id', config('global.division.code.mts'))->where('year', $this->_year)->get()->toArray();
        $data_id = array_column($data, 'id');

        $expense = MapExpense::selectRaw('ifnull(sum(amount), 0) as used')->whereIn('data_id', $data_id)->first();
        $expense = $this->convertAmount($expense->used, true);

        return array(
            'title' => 'REALISASI ANGGARAN ' . config('global.division.report.mts'),
            'series' => array('Mata Anggaran', 'Realisasi'),
            'pagu' => array(
                'color' => '#007bff',
                'value' => ($budget - $expense),
            ),
            'real' => array(
                'color' => '#007bff',
                'value' => $expense,
            ),
            'legend' => array(
                array(
                    'text' => 'Mata Anggaran',
                    'value' => $this->convertAmount($budget),
                    'color' => 'text-primary'
                ),
                array(
                    'text' => 'Realisasi',
                    'value' => $this->convertAmount($expense),
                    'percent' => $budget > 0 ? round(($expense / $budget * 100), 2) : 0.00,
                    'color' => 'text-danger'
                ),
                array(
                    'text' => 'Sisa',
                    'value' => $this->convertAmount($budget - $expense),
                    'percent' => $budget > 0 ? round((($budget - $expense) / $budget * 100), 2) : 0.00,
                    'color' => 'text-gray'
                ),
            ),
        );
    }
}
