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

    protected $fillable = [
        'data_id',
        'staff_id',
    ];
}
