<?php

namespace App\Http\Controllers;

use App\Models\DataLog;
use App\Models\Privilege;
use App\Models\UserLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function hasPrivilege($code) 
    {
        if(!in_array($code, Auth::user()->privilege)) {
            return false;
        }
        return true;
    }

    public function fixArray($param) 
    {
        foreach($param as $key => $value) {
            if(Str::containsAll($key, ['[', ']'])) {
                $newKey = Str::before($key, '[');
                $param[$newKey][Str::between($key, '[', '][')][Str::between($key, '][', ']')] = $value;
                unset($param[$key]);
            }
        }

        return $param;
    }

    public function writeAppLog($privilege, $description = '') 
    {
        $staticId = array_combine(config('global.privilege.static.code'), config('global.privilege.static.id'));
        $staticDesc = array_combine(config('global.privilege.static.code'), config('global.privilege.static.desc'));

        if(in_array($privilege, config('global.privilege.static.code'))) {
            $id = $staticId[$privilege];
            $desc = $staticDesc[$privilege];
        } else {
            $privilege = Privilege::select('id', 'desc')->where('code', $privilege)->first();
            if($privilege) {
                $id = $privilege->id;
                $desc = $privilege->desc;
            }
        }

        if(!$id) {
            dd('Error User Log Parameter : unknown privilege id');
        }

        UserLog::create([
            'username' => Auth::user()->username,
            'privilege_id' => $id,
            'description' => $desc . ($description != '' ? ' : ' .  $description : ''),
            'ip_address' => Request::getClientIp(true),
            'agent' => Browser::deviceType().' : '.Browser::platformName().' : '.Browser::browserName(),
        ]);
    }

    public function writeDataLog($filename)
    {
        DataLog::create([
            'filename' => $filename,
            'username' => Auth::user()->username,
            'ip_address' => Request::getClientIp(true),
            'agent' => Browser::deviceType().' : '.Browser::platformName().' : '.Browser::browserName(),
        ]);
    }

    public function getDivisions()
    {
        $division = array_combine(config('global.division.code'), config('global.division.desc'));

        if(Auth::user()->division_id != 0) {
            $division = Arr::only($division, [Auth::user()->division_id]);
        }

        $result = array();
        foreach($division as $key => $value) {
            $result[] = array(
                'id' => $key,
                'name' => $value,
            );
        }

        return $result;
    }

    public function getStaffs()
    {
        $staff = array_combine(config('global.staff.code'), config('global.staff.desc'));
        $result = array();
        foreach($staff as $key => $value) {
            $result[] = array(
                'id' => $key,
                'name' => $value,
            );
        }

        return $result;
    }

    public function getYears()
    {
        $result = array();
        for($i = 2022; $i < 2029; $i++) {
            $result[] = array(
                'id' => $i,
                'name' => $i.'/'.($i+1),
            );
        }

        return $result;
    }

    public function getTypes()
    {
        $types = array_combine(config('global.type.code'), config('global.type.desc'));
        $result = array();
        foreach($types as $key => $value) {
            $result[] = array(
                'id' => $key,
                'name' => $value,
            );
        }

        return $result;
    }

    public function convertAmount($value, $resverse = false) 
    {
        if($resverse) {
            return preg_replace("/[^0-9.]/", "", $value);
        } else {
            return number_format($value, 0, null, ',');
        }
    }
    
}
