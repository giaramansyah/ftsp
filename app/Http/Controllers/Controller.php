<?php

namespace App\Http\Controllers;

use App\Models\DataLog;
use App\Models\Employee;
use App\Models\Privilege;
use App\Models\UserLog;
use App\Models\Year;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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

    public function writeAppLog($privilege, $description = '', $username = null) 
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
            'username' => isset(Auth::user()->username) ? Auth::user()->username : $username,
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

    public function getCompactDivisions()
    {
        $division = array_combine(config('global.compact_division.code'), config('global.compact_division.desc'));

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
        $years = Year::where('is_trash', 0)->orderBy('year')->get();
        foreach($years as $value) {
            $result[] = array(
                'id' => $value->year,
                'name' => $value->years,
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

    public function getTransactions()
    {
        $transaction = array_combine(config('global.transaction.code'), config('global.transaction.desc'));
        $result = array();
        foreach($transaction as $key => $value) {
            $result[] = array(
                'id' => $key,
                'name' => $value,
            );
        }

        return $result;
    }

    public function getUnits()
    {
        $staff = array_combine(config('global.unit.code'), config('global.unit.desc'));
        $result = array();
        foreach($staff as $key => $value) {
            $result[] = array(
                'id' => $key,
                'name' => Str::ucfirst($value),
            );
        }

        return $result;
    }

    public function getEmployees()
    {
        $employee = Employee::select('id', 'name')->where('is_trash', 0)->get()->toArray();
        $result = array();
        foreach($employee as $key => $value) {
            $result[] = array(
                'id' => $value['id'],
                'name' => $value['name'],
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

    public function getFile($filename, $path) {
        $arr = explode('_', $filename);
        $arrDate = Arr::only($arr, [0, 1, 2]);
        $arrName = Arr::except($arr, [0, 1, 2, 3, 4, 5]);

        $year = Carbon::createFromFormat('d M Y', implode(' ', $arrDate))->year;
        $month = Carbon::createFromFormat('d M Y', implode(' ', $arrDate))->month;
        if($month < 10) {
            $month = '0'.$month;
        }
        $name = implode('_', $arrName);
        $descMonth = config('global.months');

        return (object) array('name' => $name, 'path' => $path.'/'.$year.'/'.$descMonth[$month].'/'.$filename);
    }
    
}
