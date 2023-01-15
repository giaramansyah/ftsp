<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $table = 'ms_data';

    protected $appends = ['staff', 'division', 'years', 'staff_id'];
    
    protected $fillable = [
        'ma_id',
        'description',
        'year',
        'division_id',
        'amount',
        'filename',
        'created_by',
        'updated_by',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function staffs()
    {
        return $this->hasMany(MapData::class, 'data_id', 'id');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountAttribute()
    {
        return number_format($this->attributes['amount'], 0, null, ',');
    }

    public function getYearsAttribute()
    {
        if(isset($this->attributes['year'])) {
            return $this->attributes['year'] . '/' . ($this->attributes['year']+1);
        }
    }

    public function getStaffIdAttribute()
    {
        if(isset($this->attributes['id'])) {
            $staffs = self::find($this->id)->staffs()->get()->toArray();
            return array_column($staffs, 'staff_id');
        }
        return array();
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['id'])) {
            $staffs = self::find($this->id)->staffs()->get()->toArray();
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            $descStaff = array();
            foreach($staffs as $staff) {
                if(isset($staff['staff_id']) && $staff['staff_id'] != 0) {
                    $descStaff[] = $arrStaff[$staff['staff_id']];
                }
            }

            return implode(', ', $descStaff);
        }
        return '';
    }

    public function getDivisionAttribute()
    {
        if(isset($this->attributes['division_id']) && $this->attributes['division_id'] != 0) {
            $arrDivision = array_combine(config('global.division.code'), config('global.division.desc'));
            return $arrDivision[$this->attributes['division_id']];
        }
        return '';
    }
}
