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
use App\Models\Year;
use Illuminate\Http\Request;
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
        // $employee = $this->convertAmount(Employee::count());
        $data = $this->convertAmount(Data::where('year', $this->_year)->sum('amount'));
        $balance = Balance::select('division_id', 'amount')->get();

        $years = $this->getYears();
        $yearsDesc = array_combine(array_column($years, 'id'), array_column($years, 'name'));

        $result = array(
            // array(
            //     'title' => 'Jumlah',
            //     'label' => 'Tendik & Dosen',
            //     'value' => $employee,
            //     'class' => 'bg-secondary',
            //     'prefix' => 'Orang',
            //     'icon' => 'fas fa-users',
            //     'is_prepend' => false,
            //     'is_append' => true, 
            // ),
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

        $view = ['result' => $result];

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

    public function getNote()
    {
        $years = $this->getYears();
        $result = array(
            'series' => array(),
            'requested' => array(),
            'approved' => array()

        );
        foreach($years as $year) {
            $result['series'][] = $year['name'];
            $request = Note::where('year', $year['id'])->sum('amount_requested');
            $result['requested'][] = $request ? $request : 0;
            $approve = Note::where('year', $year['id'])->sum('amount_approved');
            $result['approved'][] = $approve ? $approve : 0;
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
