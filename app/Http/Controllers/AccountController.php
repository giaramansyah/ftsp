<?php

namespace App\Http\Controllers;

use App\Library\SecureHelper;
use App\Models\MapPrivilege;
use App\Models\Menu;
use App\Models\ParentMenu;
use App\Models\Privilege;
use App\Models\PrivilegeGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index() 
    {
        $user = User::find(Auth::user()->id)->toArray();
        
        if(!$user) {
            return abort(404);
        }

        $modules = array_combine(config('global.modules.code'), config('global.modules.desc'));

        $parent = ParentMenu::select(['id','label', 'alias'])->where('is_active', 1)->orderBy('id')->get()->toArray();
        foreach($parent as $key => $value) {
            foreach($value['menu'] as $k => $val) {
                if(in_array($value['alias'], config('global.privilege.hidden'))) {
                    unset($parent[$key]['menu'][$k]);
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
                $parent[$key]['menu'][$k]['privileges'] = $privilege;
            }

        }

        $privigroup = PrivilegeGroup::select(['name', 'description'])->where('id', $user['privilege_group_id'])->first()->toArray();
        $privileges = array_column(MapPrivilege::select('privilege_id')->where('privilege_group_id', $user['privilege_group_id'])->get()->toArray(), 'privilege_id');

        $user['id'] = SecureHelper::secure(Auth::user()->id);
        $user['privilege_name'] = $privigroup['name'];
        $user['privilege_desc'] = $privigroup['description'];
        $user['username_enc'] = SecureHelper::secure($user['username']);

        $view = ['modulesArr' => $modules, 'privilegeArr' => $parent, 'privileges' => $privileges];

        return view('contents.account.index', array_merge($view,$user));
    }
}
