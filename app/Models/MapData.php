<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapData extends Model
{
    use HasFactory;

    protected $table = 'map_data';

    protected $primaryKey = null;

    public $timestamps = false;
    
    public $incrementing = false;
    
    protected $appends = ['staff'];

    protected $fillable = [
        'data_id',
        'staff_id',
    ];

    public function getStaffAttribute()
    {
        $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
        return $arrStaff[$this->attributes['staff_id']];
    }
}
