<?php

namespace App\Http\Controllers;

use App\Library\SecureHelper;
use Illuminate\Http\Request;

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
}
