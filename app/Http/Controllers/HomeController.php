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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{
    protected $_year;

    function __construct()
    {
        $this->_year = 2022;
        // $this->_year = date('Y');
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
            if (Auth::user()->privilege == ['NTCR', 'NTUP', 'NTRM', 'NTRA', 'NTRD'] || in_array(Auth::user()->staff_id, [config('global.staff.code.dekan'), config('global.staff.code.wd1'), config('global.staff.code.wd2')])) {
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
        } else {
            abort(404);
        }

        $years = $this->getYears();
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));

        $note_finished = Note::where('is_trash', 0)->where('year', $year)->where('status', config('global.status.code.finished'))->count('id');
        $note_unfinished = Note::where('is_trash', 0)->where('year', $year)->where('status', config('global.status.code.unfinished'))->count('id');

        $result = array(
            'year' => $yearsDesc[$year],
            'status' => array(
                'finished' => array(
                    'title' => 'Total Status Surat',
                    'label' => 'Selesai ' . $yearsDesc[$year],
                    'value' => $note_finished,
                    'class' => 'bg-success',
                    'prefix' => 'Surat',
                    'icon' => 'fas fa-check',
                    'is_prepend' => false,
                    'is_append' => true,
                ),
                'unfinished' => array(
                    'title' => 'Total Status Surat',
                    'label' => 'Belum Selesai ' . $yearsDesc[$year],
                    'value' => $note_unfinished,
                    'class' => 'bg-danger',
                    'prefix' => 'Surat',
                    'icon' => 'fas fa-rotate',
                    'is_prepend' => false,
                    'is_append' => true,
                ),
            )
        );

        $arrData = array(
            array(
                'unit' => 'Dekan/Univ',
                'pic' => config('global.staff.code.dekan'),
                'division' => config('global.division.code.fakultas'),
            ),
            array(
                'unit' => 'WD 1',
                'pic' => config('global.staff.code.wd1'),
                'division' => config('global.division.code.fakultas'),
            ),
            array(
                'unit' => 'WD 2',
                'pic' => config('global.staff.code.wd2'),
                'division' => config('global.division.code.fakultas'),
            ),
            array(
                'unit' => 'WD 3',
                'pic' => config('global.staff.code.wd3'),
                'division' => config('global.division.code.fakultas'),
            ),
            array(
                'unit' => 'WD 4',
                'pic' => config('global.staff.code.wd4'),
                'division' => config('global.division.code.fakultas'),
            ),
            array(
                'unit' => 'Tek. Sipil',
                'pic' => config('global.staff.code.kaprodis1'),
                'division' => config('global.division.code.sipil'),
            ),
            array(
                'unit' => 'Arsitektur',
                'pic' => config('global.staff.code.kaprodis1'),
                'division' => config('global.division.code.arsitektur'),
            ),
            array(
                'unit' => 'MTS',
                'pic' => config('global.staff.code.kaprodis2'),
                'division' => config('global.division.code.mts'),
            ),
            array(
                'unit' => 'MTA',
                'pic' => config('global.staff.code.kaprodis2'),
                'division' => config('global.division.code.mta'),
            ),
        );

        foreach($arrData as $key => $value) {
            $note = Note::select('id', 'amount', 'amount_requested', 'amount_approved', 'status')->where('is_trash', 0)->where('year', $year)->where('division_id', $value['division'])->whereRelation('staffs', 'staff_id', $value['pic']);
            $data = Data::select('id', 'amount')->where('is_trash', 0)->where('year', $year)->where('division_id', $value['division'])->whereRelation('staffs', 'staff_id', $value['pic']);

            if($value['pic'] == config('global.staff.code.wd2')) {
                $note->has('staffs', '=', 1);
                $data->has('staffs', '=', 1);
            }
            
            $note = $note->get()->toArray();
            $data = $data->get()->toArray();

            $amount = 0;
            $request = 0;
            $approve = 0;
            $process = 0;
            $percentRequest = 0;
            $percentApprove = 0;
            $percentProgress = 0;
            $percentProcess = 0;
            $finished = 0;
            $unfinished = 0;

            if(!empty($note)) {
                foreach ($note as $row) {
                    $request += $this->convertAmount($row['amount_requested'], true);
                    $approve += $this->convertAmount($row['amount_approved'], true);

                    if($row['status'] == config('global.status.code.finished')) {
                        $finished += 1;
                    } else {
                        $unfinished += 1;
                    }
                }
            }

            if(!empty($data)) {
                foreach ($data as $row) {
                    $amount += $this->convertAmount($row['amount'], true);
                }
            }
            
            $process = ($request - $approve);   
            
            if($amount > 0) {
                $percentRequest = round($request/$amount*100, 2);
                $percentApprove = round($approve/$amount*100, 2);
                $percentProcess = round($process/$amount*100, 2);
            }
            
            if($request > 0) {
                $percentProgress = round($approve/$request*100, 2);
            }

            $result['series'][$key] = $value['unit'];
            $result['amount'][$key] = $amount;
            $result['requested'][$key] = $request;
            $result['approved'][$key] = $approve;
            $result['process'][$key] = $process;
            $result['percent_request'][$key] = $percentRequest;
            $result['percent_approve'][$key] = $percentApprove;
            $result['percent_progress'][$key] = $percentProgress;
            $result['percent_process'][$key] = $percentProcess;
            $result['finished'][$key] = $finished;
            $result['unfinished'][$key] = $unfinished;
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
