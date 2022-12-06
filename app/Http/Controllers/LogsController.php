<?php

namespace App\Http\Controllers;

use App\Library\SecureHelper;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LogsController extends Controller
{
    
    protected $_readid = 'LURA';

    public function index()
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }
        
        return view('contents.logs.index');
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = UserLog::select(['id', 'username', 'privilege_id', 'description', 'ip_address', 'updated_at', 'agent'])->orderBy('updated_at', 'desc');
            $table = DataTables::eloquent($data);
            $table->addColumn('privilege', function($row) {
                $column = $row->getPrivilegeCodeAttribute();
                return $column;
            });
            $rawColumns = array('privilege');

            $table->rawColumns($rawColumns);

            return $table->toJson();
        }
    }

    public function getUser(Request $request, $id)
    {
        $plainId = SecureHelper::unsecure($id);

        if(!$plainId) {
            $collection = collect();
            $table = DataTables::of($collection);
            return $table->toJson();
        }

        $user = User::where('username', $plainId)->first()->toArray();
        
        if(!$user) {
            $collection = collect();
            $table = DataTables::of($collection);
            return $table->toJson();
        }
        
        if ($request->ajax()) {
            $data = UserLog::select(['id', 'username', 'privilege_id', 'description', 'ip_address', 'updated_at', 'agent'])->where('username', $plainId)->orderBy('updated_at', 'desc');
            $table = DataTables::eloquent($data);
            $table->addColumn('privilege', function($row) {
                $column = $row->getPrivilegeCodeAttribute();
                return $column;
            });
            $rawColumns = array('privilege');

            $table->rawColumns($rawColumns);

            return $table->toJson();
        }
    }
}
