<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $table = 'ts_reception';
    
    protected $appends = ['staff', 'division', 'years', 'reception_date_format'];

    protected $fillable = [
        'reception_id',
        'reception_date',
        'year',
        'division_id',
        'description',
        'sub_description',
        'data_id',
        'ma_id',
        'name',
        'staff_id',
        'amount',
        'text_amount',
        'created_by',
        'updated_by',
    ];

    public function datas()
    {
        return $this->hasOne(Data::class, 'id', 'data_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = preg_replace("/[^0-9.]/", "", $value);
    }

    public function getAmountAttribute()
    {
        return number_format($this->attributes['amount'], 0, null, ',');
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['staff_id']) && $this->attributes['staff_id'] != 0) {
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            return $arrStaff[$this->attributes['staff_id']];
        }
        return '';
    }

    public function getReceptionDateFormatAttribute()
    {
        if(isset($this->attributes['reception_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['reception_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getYearsAttribute()
    {
        if(isset($this->attributes['year'])) {
            return $this->attributes['year'] . '/' . ($this->attributes['year']+1);
        }
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
