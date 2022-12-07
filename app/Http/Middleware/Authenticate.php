<?php

namespace App\Http\Middleware;

use App\Models\ParentMenu;
use App\Models\PrivilegeGroup;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('landing');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $currentRoute = Route::currentRouteName();

        $appender = '';
        $header = Str::ucfirst(config('global.home'));
        $breadcrumb = array(
            array('label' => Str::ucfirst(config('global.home')), 'active' => true), 
        );

        $aliases = explode('.', $currentRoute);

        $group = PrivilegeGroup::find(Auth::user()->privilege_group_id)->privileges()->get()->toArray();
        $group = array_column($group, 'menu_id');
        $menus = ParentMenu::where('is_active', 1)->orderBy('order', 'asc')->get()->toArray();
        $side_nav = array();
        foreach($menus as $value) {
            foreach($value['menu'] as $val) {
                if(in_array($val['id'], $group)) {
                    if(!array_key_exists($value['alias'], $side_nav)) {
                        $active = count($aliases) > 0 && Str::is($aliases[0], $value['alias']);

                        $side_nav[$value['alias']] = array(
                            'label' => $value['label'],
                            'alias' => $value['alias'],
                            'icon' => $value['icon'],
                            'active' => $active,
                            'menus' => array()
                        );
                    }

                    $active = count($aliases) > 1 && Str::is($aliases[1], $val['alias']);

                    if($active) {
                        $header = $val['label'];
                        $breadcrumb = array(
                            array('label' => $value['label'], 'active' => false), 
                            array('label' => $val['label'], 'active' => true), 
                        );
                    }

                    $side_nav[$value['alias']]['menus'][$val['alias']] = array(
                        'label' => $val['label'],
                        'alias' => $val['label'],
                        'url' => $val['url'],
                        'active' => $active,
                    );
                }
            }
        }

        if(in_array('add', $aliases)) {
            $appender = 'Tambah Baru';
            $breadcrumb[1]['active'] = false;
            $breadcrumb[] = array('label' => $appender, 'active' => true);
        }

        if(in_array('edit', $aliases)) {
            $appender = 'Ubah';
            $breadcrumb[1]['active'] = false;
            $breadcrumb[] = array('label' => $appender, 'active' => true);
        }
        
        if(in_array('view', $aliases)) {
            $appender = 'Detil';
            $breadcrumb[1]['active'] = false;
            $breadcrumb[] = array('label' => $appender, 'active' => true);
        }

        

        View::share('header', $header);
        View::share('appender', $appender);
        View::share('breadcrumb', $breadcrumb);
        View::share('side_nav', $side_nav);
        
        return $next($request);
    }
}
