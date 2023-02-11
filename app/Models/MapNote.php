<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapNote extends Model
{
    use HasFactory;

    protected $table = 'map_note';

    protected $primaryKey = null;

    public $timestamps = false;
    
    public $incrementing = false;
    
    protected $appends = ['staff'];

    protected $fillable = [
        'note_id',
        'staff_id',
    ];

    public function getStaffAttribute()
    {
        if($this->attributes['staff_id'] > 0) {
            $arrStaff = array_combine(config('global.staff.code'), config('global.staff.desc'));
            return $arrStaff[$this->attributes['staff_id']];
        }

        return '';
    }
}
