<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\MapPrivilege;
use App\Models\Menu;
use App\Models\Privilege;
use App\Models\PrivilegeGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PrivilegeGroupController extends Controller
{
    protected $_create = 'PGCR';
    protected $_update = 'PGUP';
    protected $_delete = 'PGRM';
    protected $_readall = 'PGRA';

    public function index()
    {
        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.privigroup.index', ['is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function add()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $menu = Menu::select(['id', 'label', 'alias'])->where('is_active', 1)->orderBy('id')->get()->toArray();
        foreach ($menu as $key => $value) {
            if (in_array($value['alias'], config('global.privilege.hidden'))) {
                unset($menu[$key]);
                continue;
            }

            $items = array_map(function ($val) {
                array();
            }, $modules);
            $privilege = Privilege::select(['id', 'code', 'modules'])->where('menu_id', $value['id'])->orderBy('modules')->get()->toArray();

            foreach ($privilege as $value) {
                $items[$value['modules']] = $value;
            }

            $menu[$key]['privileges'] = $items;
        }

        $view = ['modulesArr' => $modules, 'privilegeArr' => $menu, 'action' => route('settings.privigroup.post', ['action' => config('global.action.form.add'), 'id' => 0])];

        return view('contents.privigroup.form', $view);
    }

    public function edit($id)
    {
        if (!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);
        if (!$plainId) {
            return abort(404);
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $menu = Menu::select(['id', 'label', 'alias'])->where('is_active', 1)->orderBy('id')->get()->toArray();
        foreach ($menu as $key => $value) {
            if (in_array($value['alias'], config('global.privilege.hidden'))) {
                unset($menu[$key]);
                continue;
            }

            $privilege = Privilege::select(['id', 'code', 'modules'])->where('menu_id', $value['id'])->orderBy('modules')->get()->toArray();
            if (count($privilege) < count($modules)) {
                $diff = count($privilege);
                while ($diff < count($modules)) {
                    $privilege[] = array();
                    $diff++;
                }
            }
            $menu[$key]['privileges'] = $privilege;
        }

        $privigroup = PrivilegeGroup::select(['name', 'description'])->where('id', $plainId)->first()->toArray();
        $privigroup['privileges'] = array_column(MapPrivilege::select('privilege_id')->where('privilege_group_id', $plainId)->get()->toArray(), 'privilege_id');

        $view = ['modulesArr' => $modules, 'privilegeArr' => $menu, 'action' => route('settings.privigroup.post', ['action' => config('global.action.form.edit'), 'id' => $id])];

        return view('contents.privigroup.form', array_merge($privigroup, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = PrivilegeGroup::latest()->where('id', '!=', config('global.sysadmin.privilege'));
            $table = DataTables::eloquent($data);
            $table->addIndexColumn();
            $table->addColumn('action', function ($row) {
                $column = '';

                if ($this->hasPrivilege($this->_update)) {
                    $param = array('class' => 'btn-xs', 'action' => route('settings.privigroup.edit', ['id' => SecureHelper::secure($row->id)]));
                    $column .= view('partials.button.edit', $param)->render();
                }

                if ($this->hasPrivilege($this->_delete)) {
                    $param = array('class' => 'btn-xs', 'source' => 'table', 'action' => route('settings.privigroup.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                    $column .= view('partials.button.delete', $param)->render();
                }

                return $column;
            });

            $table->rawColumns(['action']);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
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

                $privigroup = PrivilegeGroup::where('name', $param['name'])->first();
                if (!$privigroup) {
                    $privigroup = PrivilegeGroup::create([
                        'name' => Str::upper($param['name']),
                        'description' => $param['description'],
                        'created_by' => Auth::user()->username,
                        'updated_by' => Auth::user()->username,
                    ]);
                    if ($privigroup->id) {
                        foreach ($param['privilege_id'] as $value) {
                            MapPrivilege::create([
                                'privilege_group_id' => $privigroup->id,
                                'privilege_id' => $value,
                            ]);
                        }
                        $response = new Response(true, __('Privilege group created successfuly'), 1);
                        $response->setRedirect(route('settings.privigroup.index'));

                        $this->writeAppLog($this->_create, 'Privilege Group : ' . $param['name']);
                    } else {
                        $response = new Response(false, __('Privilege group create failed. Please try again'));
                    }
                } else {
                    $response = new Response(false, __('Privilege group name already exist'));
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

                $privigroup = PrivilegeGroup::find($plainId);

                $privigroup->name = Str::upper($param['name']);
                $privigroup->description = $param['description'];
                $privigroup->updated_at = Carbon::now()->toDateTimeString();

                if ($privigroup->save()) {
                    $map = MapPrivilege::where('privilege_group_id', $plainId);
                    $map->forceDelete();
                    foreach ($param['privilege_id'] as $value) {
                        MapPrivilege::create([
                            'privilege_group_id' => $privigroup->id,
                            'privilege_id' => $value,
                        ]);
                    }
                    $response = new Response(true, __('Privilege group updated successfuly'), 1);
                    $response->setRedirect(route('settings.privigroup.index'));

                    $this->writeAppLog($this->_update, 'Privilege Group : ' . $param['name']);
                } else {
                    $response = new Response(false, __('Privilege group update failed. Please try again'));
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

            $privigroup = PrivilegeGroup::find($plainId);
            $param = $privigroup->name;
            $privigroup->forceDelete();
            MapPrivilege::where('privilege_group_id', $plainId)->forceDelete();

            $response = new Response(true, __('Privilege group deleted successfuly'), 1);
            $response->setRedirect(route('settings.privigroup.index'));

            $this->writeAppLog($this->_delete, 'Privilege Group : ' . $param);
        }

        return response()->json($response->responseJson());
    }
}
