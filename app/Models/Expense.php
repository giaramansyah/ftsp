<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'ts_expense';
    
    protected $appends = ['staff', 'status_desc', 'name_desc', 'expense_date_format', 'apply_date_format', 'reff_date_format'];

    protected $fillable = [
        'expense_id',
        'expense_date',
        'reff_no',
        'reff_date',
        'description',
        'sub_description',
        'data_id',
        'ma_id',
        'name',
        'staff_id',
        'amount',
        'text_amount',
        'account',
        'apply_date',
        'image',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];

    public function datas()
    {
        return $this->hasOne(Data::class, 'id', 'data_id');
    }

    public function names()
    {
        return $this->hasOne(Employee::class, 'id', 'name');
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

    public function getNameDescAttribute()
    {
        if(isset($this->attributes['name']) && $this->attributes['name'] != '') {
            $employee = self::find($this->id)->names()->where('id', $this->attributes['name'])->first();
            if($employee) {
                return $employee->name;
            } else {
                return $this->attributes['name'];
            }
        }
        return '';
    }

    public function getStaffAttribute()
    {
        if(isset($this->attributes['staff_id']) && $this->attributes['staff_id'] != 0) {
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            return $arrStaff[$this->attributes['staff_id']];
        }
        return '';
    }

    public function getStatusDescAttribute()
    {   
        $status = array();

        if(isset($this->attributes['type']) && $this->attributes['type'] != 0) {
            $arrType = array_combine(config('global.type.code'), config('global.type.status'));
            $status[] = $arrType[$this->attributes['type']];
        }

        if(isset($this->attributes['status']) && $this->attributes['status'] != 0 && $this->attributes['type'] == config('global.type.code.white')) {
            $arrStatus = array_combine(config('global.status.code'), config('global.status.desc'));
            $status[] = $arrStatus[$this->attributes['status']];
        }

        return implode(' - ', $status);
    }

    public function getExpenseDateFormatAttribute()
    {
        if(isset($this->attributes['expense_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['expense_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getReffDateFormatAttribute()
    {
        if(isset($this->attributes['reff_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['reff_date']);
            return $date->format('d M Y');
        }
        return '';
    }

    public function getApplyDateFormatAttribute()
    {
        if(isset($this->attributes['apply_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $this->attributes['apply_date']);
            return $date->format('d M Y');
        }
        return '';
    }

}
