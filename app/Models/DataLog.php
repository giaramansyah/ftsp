<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLog extends Model
{
    use HasFactory;

    protected $table = 'log_data';

    protected $fillable = [
        'filename',
        'username',
        'ip_address',
        'agent',
    ];
}
