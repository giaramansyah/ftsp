<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'ms_user';

    protected $appends = ['fullname', 'privilege', 'division', 'staff'];
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'division_id',
        'staff_id',
        'privilege_group_id',
        'is_login',
        'is_trash',
        'is_new',
        'password',
        'hash',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'hash',
        'remember_token',
    ];
    
    public function group() {
        return $this->hasOne(PrivilegeGroup::class, 'id', 'privilege_group_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getFullnameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPrivilegeAttribute()
    {
        $group = self::find($this->id)->group()->first()->privileges()->get()->toArray();
        return array_column($group, 'code');
    }

    public function getDivisionAttribute()
    {
        if(isset($this->attributes['division_id']) && $this->attributes['division_id'] != 0) {
            $arrDivision = array_combine(config('global.division.code'), config('global.division.desc'));
            return $arrDivision[$this->attributes['division_id']];
        }
        return '';
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['staff_id'])) {
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            return $arrStaff[$this->attributes['staff_id']];
        }
        return '';
    }
}
