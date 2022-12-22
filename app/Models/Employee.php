<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;
    
    protected $table = 'ms_employee';
    
    protected $appends = ['unit'];

    protected $fillable = [
        'unit_id',
        'nik',
        'name',
        'account',
        'is_trash',
        'created_by',
        'updated_by',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('global.dateformat.view'));
    }

    public function getUnitAttribute()
    {
        if(isset($this->attributes['unit_id']) && $this->attributes['unit_id'] != 0) {
            $units = array_combine(config('global.unit.code'), config('global.unit.desc'));
            return Str::ucfirst($units[$this->attributes['unit_id']]);
        }
    }
}
