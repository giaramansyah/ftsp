<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapExpense extends Model
{
    use HasFactory;

    protected $table = 'map_expense';

    protected $primaryKey = null;

    public $timestamps = false;
    
    public $incrementing = false;

    protected $fillable = [
        'expense_id',
        'data_id',
        'amount',
    ];

    public function datas()
    {
        return $this->hasOne(Data::class, 'id', 'data_id');
    }
}
