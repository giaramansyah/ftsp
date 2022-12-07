<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    protected $_create = 'TRCR';
    protected $_update = 'TRUP';
    protected $_readall = 'TRRA';
    protected $_readid = 'TRRD';

    public function index($year)
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $currYear = date('Y');
        if($year) {
            $plainYear = SecureHelper::unsecure($year);

            if($plainYear) {
                $currYear = $plainYear;
            }
        }

        $years = $this->getYears();
        $division = $this->getDivisions();

        return view('contents.offer.index', ['divisionArr' =>  $division, 'yearArr' =>  $years, 'year' => $currYear, 'is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function add()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $year = $this->getYears();
        $type = $this->getTypes();

        $view = ['yearArr' => $year, 'typeArr' => $type, 'action' => route('transaction.offer.add'), 'mandatory' => $this->hasPrivilege($this->_create), 'is_multi' => false];

        return view('contents.offer.form', $view);
    }

    public function edit($id)
    {
        
    }

    public function getList(Request $request)
    {
        
    }

    public function generate(Request $request)
    {
        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $year = Str::after($param['year'], '20');
        if($param['type_id'] == config('global.type.code.green')) {
            $prefix = 'I';
        } else if($param['type_id'] == config('global.type.code.white') || $param['type_id'] == config('global.type.code.red')) {
            $prefix = 'PM';
        } else {
            $response = new Response();
            return response()->json($response->responseJson());
        }
        $number = '0001';

        $offer = Offer::select('offer_no')->where('offer_no', 'like', "%$$prefix$year%")->orderBy('offer_no', 'desc')->first();
        if($offer) {
            $currentNumber = Str::after($offer->offer_no, $prefix.$year);
            $currentNumber++;
            $currentNumber = strval($currentNumber);
            $temp = '';
            for($i = 0; $i < 4 - strlen($currentNumber); $i++) {
                $temp .= '0';
            }

            $number = $temp.$currentNumber;
        } 

        $id = $prefix.$year.$number;

        $response = new Response(true, __('ID generated successfuly'), 1);
        $response->setData(['id' => $id]);
        return response()->json($response->responseJson());
    }
}
