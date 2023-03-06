<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class YearController extends Controller
{
    protected $_create = 'YRCR';
    protected $_delete = 'YRRM';
    protected $_readall = 'YRRA';

    public function index() 
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $year = Year::where('is_trash', 0)->orderBy('year', 'desc')->first();
        $id = date('Y');
        if($year) {
            $id = ($year->year+1);
        }

        return view('contents.year.index', ['year' => $id, 'is_create' => $this->hasPrivilege($this->_create), 'action' => route('master.years.post', ['action' => config('global.action.form.add'), 'id' => 0 ])]);
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = Year::where('is_trash', 0)->orderBy('year');
            $table = DataTables::eloquent($data);
            $rawColumns = array('action');
            $table->addIndexColumn();

            if($this->hasPrivilege($this->_delete)) {
                $table->addColumn('action', function($row) {
                    $column = '';

                    if($this->hasPrivilege($this->_delete)) {
                        $param = array('class' => 'btn-xs', 'source' => 'table', 'action' => route('master.years.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                        $column .= View::render('partials.button.delete', $param);
                    }

                    return $column;
                });
            }

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function post(Request $request, $action, $id)
    {
        if(!in_array($action, config('global.action.form'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if($action === config('global.action.form.add')) {
            $param = SecureHelper::unpack($request->input('json'));

            if (!is_array($param)) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if(!$this->hasPrivilege($this->_create)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $year = Year::create([
                'year' => $param['year'],
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ]);
            if($year->id) {
                $response = new Response(true, __('Year created successfuly'), 1);
                $response->setRedirect(route('master.years.index'));

                $this->writeAppLog($this->_create, 'Year : '.$year->year);
            } else {
                $response = new Response(false, __('Year create failed. Please try again'));
            }
        }

        if($action === config('global.action.form.delete')) {
            if(!$this->hasPrivilege($this->_delete)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $plainId = SecureHelper::unsecure($id);

            if(!$plainId) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            $year = Year::find($plainId);
            $year->is_trash = 1;

            if($year->save()) {
                $response = new Response(true, __('Year deleted successfuly'), 1);
                $response->setRedirect(route('master.years.index'));

                $this->writeAppLog($this->_delete, 'Year : '.$year->year);
            } else {
                $response = new Response(false, __('Year delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }

}
