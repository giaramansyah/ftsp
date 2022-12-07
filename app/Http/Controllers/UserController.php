<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\SecureHelper;
use App\Library\Response;
use App\Models\MapPrivilege;
use App\Models\Menu;
use App\Models\Privilege;
use App\Models\PrivilegeGroup;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $_create = 'USCR';
    protected $_update = 'USUP';
    protected $_delete = 'USRM';
    protected $_readall = 'USRA';
    protected $_readid = 'USRD';

    public function index() 
    {
        if(!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        return view('contents.user.index', ['is_create' => $this->hasPrivilege($this->_create)]);
    }

    public function add()
    {
        if(!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $group = PrivilegeGroup::select(['id', 'name'])->where('id', '!=', config('global.sysadmin.privilege'))->get()->toArray();
        foreach($group as $key => $value) {
            $group[$key]['id'] = SecureHelper::secure($value['id']);
        }

        $division = $this->getDivisions();
        $staff = $this->getStaffs();

        $view = ['groupArr' => $group, 'divisionArr' => $division, 'staffArr' => $staff, 'action' => route('settings.user.post', ['action' => config('global.action.form.add'), 'id' => 0 ]), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.user.form', $view);
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

        $user = User::find($plainId)->toArray();
        
        if(!$user) {
            return abort(404);
        }

        if(!$this->hasPrivilege($this->_readid)) {
            $user = array('username' => $user['username']);
        }

        $group = PrivilegeGroup::select(['id', 'name'])->where('id', '!=', config('global.sysadmin.privilege'))->get()->toArray();
        foreach($group as $key => $value) {
            $encId = SecureHelper::secure($value['id']);
            if(isset($user['privilege_group_id']) && $user['privilege_group_id'] == $value['id']) {
                $user['privilege_group_id'] = $encId;
            }
            $group[$key]['id'] = $encId;
        }

        $division = $this->getDivisions();
        $division = Arr::except($division, 0);
        $staff = $this->getStaffs();
        
        $view = ['groupArr' => $group, 'divisionArr' => $division, 'staffArr' => $staff, 'action' => route('settings.user.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'mandatory' => $this->hasPrivilege($this->_readid)];

        return view('contents.user.form', array_merge($user, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select(['id', 'username', 'email', 'is_login', 'updated_at', 'first_name', 'last_name'])->where('username', '!=', config('global.sysadmin.username'))->where('is_trash', 0)->orderBy('id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('user_fullname', 'login');
            $table->addIndexColumn();

            $table->addColumn('user_fullname', function($row) {
                if($this->hasPrivilege($this->_readid)) {
                    $column = $row->getFullnameAttribute() . ' (<a href="'.route('settings.user.view', ['id' => SecureHelper::secure($row->id)]).'">' . $row->username . '</a>)';
                } else {
                    $column = $row->getFullnameAttribute() . ' (' . $row->username . ')';
                }

                return $column;
            });


            if($this->hasPrivilege($this->_update) || $this->hasPrivilege($this->_delete)) {
                $table->addColumn('action', function($row) {
                    $column = '';

                    if($this->hasPrivilege($this->_update)) {
                        $param = array('class' => 'btn-xs', 'action' => route('settings.user.edit', ['id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.edit', $param)->render();
                    }

                    if($this->hasPrivilege($this->_delete)) {
                        $param = array('class' => 'btn-xs', 'source' => 'table', 'action' => route('settings.user.post', ['action' => config('global.action.form.delete'), 'id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.delete', $param)->render();
                    }

                    if($row->is_login) {
                        $param = array('class' => 'btn-xs', 'action' => route('settings.user.post', ['action' => 'force_logout', 'id' => SecureHelper::secure($row->id)]));
                        $column .= view('partials.button.forcelogout', $param)->render();
                    }

                    return $column;
                });

                $rawColumns[] = 'action';
            }

            $table->addColumn('login', function($row) {
                if($row->is_login) {
                    $column = '<i class="fas fa-check-circle text-success"></i> '.__('Linked');
                } else {
                    $column = '<i class="fas fa-times-circle text-danger"></i> '.__('Unlinked');
                }

                return $column;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function view($id)
    {
        if(!$this->hasPrivilege($this->_readid)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if(!$plainId) {
            return abort(404);
        }

        $user = User::find($plainId)->toArray();
        
        if(!$user) {
            return abort(404);
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $menu = Menu::select(['id','label', 'alias'])->where('is_active', 1)->orderBy('id')->get()->toArray();
        foreach($menu as $key => $value) {
            if(in_array($value['alias'], config('global.privilege.hidden'))) {
                unset($menu[$key]);
                continue;
            }

            $privilege = Privilege::select(['id','code', 'modules'])->where('menu_id', $value['id'])->orderBy('modules')->get()->toArray();
            if(count($privilege) < count($modules)) {
                $diff = count($privilege);
                while($diff < count($modules)) {
                    $privilege[] = array();
                    $diff++;
                }
            }
            $menu[$key]['privileges'] = $privilege;
        }


        $privigroup = PrivilegeGroup::select(['name', 'description'])->where('id', $user['privilege_group_id'])->first()->toArray();
        $privileges = array_column(MapPrivilege::select('privilege_id')->where('privilege_group_id', $user['privilege_group_id'])->get()->toArray(), 'privilege_id');

        $user['id'] = $id;
        $user['privilege_name'] = $privigroup['name'];
        $user['privilege_desc'] = $privigroup['description'];
        $user['username_enc'] = SecureHelper::secure($user['username']);

        $view = ['modulesArr' => $modules, 'privilegeArr' => $menu, 'privileges' => $privileges, 'is_update' => $this->hasPrivilege($this->_update), 'is_update' => $this->hasPrivilege($this->_delete)];

        $this->writeAppLog($this->_readid, 'User Account : '.$user['username']);

        return view('contents.user.view', array_merge($view,$user));
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

                $user = User::where('username', $param['username'])->where('is_trash', 0)->first();
                if(!$user) {
                    $user = User::where('email', $param['email'])->where('is_trash', 0)->first();
                    if(!$user) {
                        $hash = md5(sha1(date('ymdHis')));
                        $password = $param['username'].$param['username'].$hash;
                        $user = User::create([
                            'first_name' => $param['first_name'],
                            'last_name' => $param['last_name'],
                            'email' => $param['email'],
                            'username' => $param['username'],
                            'password' => $password,
                            'hash' => $hash,
                            'staff_id' => $param['staff_id'],
                            'division_id' => $param['staff_id'] == 1 ? 0 : $param['division_id'],
                            'privilege_group_id' => SecureHelper::unsecure($param['privilege_group_id']),
                            'created_by' => Auth::user()->username,
                            'updated_by' => Auth::user()->username,
                        ]);
                        if($user->id) {
                            $response = new Response(true, __('Account created successfuly'), 1);
                            $response->setRedirect(route('settings.user.index'));

                            $this->writeAppLog($this->_create, 'User Account : '.$param['username']);
                        } else {
                            $response = new Response(false, __('Account create failed. Please try again'));
                        }
                    } else {
                        $response = new Response(false, __('Account with this email already registered'));
                    }
                } else{
                    $response = new Response(false, __('Account with this username already registered'));
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

                if(!$this->hasPrivilege($this->_readid)) {
                    $user = User::find($plainId);
                    if(isset($param['email']) && $param['email'] != '') {
                        $exist = User::where('email', $param['email'])->where('id', '!=', $plainId)->where('is_trash', 0)->first();
                        if($exist) {
                            $response = new Response(false, __('Account with this email already registered'));
                            return response()->json($response->responseJson());
                        }
                    }

                    if(isset($param['first_name']) && $param['first_name'] != '') $user->first_name = $param['first_name'];
                    if(isset($param['last_name']) && $param['last_name'] != '') $user->last_name = $param['last_name'];
                    if(isset($param['email']) && $param['email'] != '') $user->email = $param['email'];
                    if(isset($param['staff_id']) && $param['staff_id'] != '') $user->staff_id = $param['staff_id'];
                    if(isset($param['division_id']) && $param['division_id'] != '') $user->division_id = $param['staff_id'] == 1 ? 0 : $param['division_id'];
                    if(isset($param['privilege_group_id']) && $param['privilege_group_id'] != '') $user->privilege_group_id = SecureHelper::unsecure($param['privilege_group_id']);
                    $user->updated_by = Auth::user()->username;

                    if($user->save()) {
                        $response = new Response(true, __('Account updated successfuly'), 1);
                        $response->setRedirect(route('settings.user.index'));

                        $this->writeAppLog($this->_update, 'User Account : '.$param['username']);
                    } else {
                        $response = new Response(false, __('Account update failed. Please try again'));
                    }
                } else {
                    $user = User::where('email', $param['email'])->where('id', '!=', $plainId)->where('is_trash', 0)->first();
                    if(!$user) {
                        $user = User::find($plainId);
                        $user->first_name = $param['first_name'];
                        $user->last_name = $param['last_name'];
                        $user->email = $param['email'];
                        $user->staff_id = $param['staff_id'];
                        $user->division_id = $param['staff_id'] == 1 ? 0 : $param['division_id'];
                        $user->privilege_group_id = SecureHelper::unsecure($param['privilege_group_id']);
                        $user->updated_by = Auth::user()->username;
    
                        if($user->save()) {
                            $response = new Response(true, __('Account updated successfuly'), 1);
                            $response->setRedirect(route('settings.user.index'));
    
                            $this->writeAppLog($this->_update, 'User Account : '.$param['username']);
                        } else {
                            $response = new Response(false, __('Account update failed. Please try again'));
                        }
                    } else {
                        $response = new Response(false, __('Account with this email already registered'));
                    }
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

            $user = User::find($plainId);
            $user->is_trash = 1;

            if($user->save()) {
                $response = new Response(true, __('Account deleted successfuly'), 1);
                $response->setRedirect(route('settings.user.index'));

                $this->writeAppLog($this->_delete, 'User Account : '.$user->username);
            } else {
                $response = new Response(false, __('Account delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }
}
