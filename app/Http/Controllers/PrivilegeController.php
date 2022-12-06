<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\MapPrivilege;
use App\Models\Menu;
use App\Models\ParentMenu;
use App\Models\Privilege;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PrivilegeController extends Controller
{
    protected $_create = 'PRCR';
    protected $_update = 'PRUP';
    protected $_delete = 'PRRM';
    protected $_readall = 'PRRA';

    public function index()
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.privilege.index');
    }

    public function add()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $menu = ParentMenu::select(['id', 'label'])->where('is_active', 1)->get()->toArray();
        foreach($menu as $key => $value) {
            $menu[$key]['menu'] = ParentMenu::find($value['id'])->menus()->select(['id', 'label'])->where('is_active', 1)->get()->toArray();
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $view = ['menuArr' => $menu, 'modulesArr' => $modules, 'action' => route('settings.privilege.post', ['action' => config('global.action.form.add'), 'id' => 0 ])];

        return view('contents.privilege.form', $view);
    }

    public function edit($id)
    {
        if(!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }
        
        $plainId = SecureHelper::unsecure($id);
        if(!$plainId) {
            return abort(404);
        }

        $menu = ParentMenu::select(['id', 'label'])->where('is_active', 1)->get()->toArray();
        foreach($menu as $key => $value) {
            $menu[$key]['menu'] = ParentMenu::find($value['id'])->menus()->select(['id', 'label'])->where('is_active', 1)->get()->toArray();
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $privilege = Privilege::find($plainId)->toArray();

        if(!$privilege) {
            return abort(404);
        }

        $view = ['menuArr' => $menu, 'modulesArr' => $modules, 'action' => route('settings.privilege.post', ['action' => config('global.action.form.edit'), 'id' => $id])];

        return view('contents.privilege.form', array_merge($privilege, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = Privilege::select(['id', 'code', 'modules', 'desc'])->orderBy('id');
            $table = DataTables::eloquent($data);
            $table->addIndexColumn();
            if($this->hasPrivilege($this->_update) || $this->hasPrivilege($this->_delete)) {
                $table->addColumn('action', function($row) {
                    $column = '';

                    if($this->hasPrivilege($this->_update)) {
                        $param = array('class' => 'btn-xs', 'action' => route('settings.privilege.edit', ['id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.edit', $param)->render();
                    }

                    if($this->hasPrivilege($this->_delete)) {
                        $param = array('class' => 'btn-xs', 'action' => route('settings.privilege.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.delete', $param)->render();
                    }

                    return $column;
                });

                $table->rawColumns(['action']);
            }

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

        if(in_array($action, Arr::only(config('global.action.form'), ['add', 'edit']))) {
            $param = SecureHelper::unpack($request->input('json'));

            if (!is_array($param)) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if($action === config('global.action.form.add')) {
                if(!$this->hasPrivilege($this->_create)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $privilege = Privilege::where('code', $param['code'])->first();
                if(!$privilege) {
                    $privilege = Privilege::create([
                        'code' => Str::upper($param['code']),
                        'menu_id' => $param['menu_id'],
                        'modules' => $param['modules'],
                        'desc' => $param['desc'],
                    ]);
                    if($privilege->id) {
                        MapPrivilege::create([
                            'privilege_group_id' => config('global.sysadmin.privilege'),
                            'privilege_id' => $privilege->id,
                        ]);
                        $response = new Response(true, __('Privilege created successfuly'), 1);
                        $response->setRedirect(route('settings.privilege.index'));

                        $this->writeAppLog($this->_create, 'Privilege : '.$param['code']);
                    } else {
                        $response = new Response(false, __('Privilege create failed. Please try again'));
                    }
                } else{
                    $response = new Response(false, __('Privilege code already exist'));
                }
            }

            if($action === config('global.action.form.edit')) {
                if(!$this->hasPrivilege($this->_update)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $plainId = SecureHelper::unsecure($id);
                if(!$plainId) {
                    $response = new Response();
                    return response()->json($response->responseJson());
                }

                $privilege = Privilege::find($plainId);

                $privilege->menu_id = $param['menu_id'];
                $privilege->modules = $param['modules'];
                $privilege->desc = $param['desc'];

                if($privilege->save()) {
                    $response = new Response(true, __('Privilege updated successfuly'), 1);
                    $response->setRedirect(route('settings.privilege.index'));

                    $this->writeAppLog($this->_update, 'Privilege : '.$param['code']);
                } else {
                    $response = new Response(false, __('Privilege update failed. Please try again'));
                }
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

            $privilege = Privilege::find($plainId);
            $param = $privilege->code;
            $privilege->forceDelete();
            MapPrivilege::where('privilege_id', $plainId)->forceDelete();

            $response = new Response(true, __('Privilege deleted successfuly'), 1);
            $response->setRedirect(route('settings.privilege.index'));

            $this->writeAppLog($this->_delete, 'Privilege : '.$param);
        }

        return response()->json($response->responseJson());
    }
}
