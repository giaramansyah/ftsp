<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapReport extends Model
{
    use HasFactory;

    protected $table = 'map_report';

    protected $primaryKey = null;

    public $timestamps = false;
    
    public $incrementing = false;

    protected $fillable = [
        'report_id',
        'data_id',
        'is_reception',
        'is_expense',
    ];
}
