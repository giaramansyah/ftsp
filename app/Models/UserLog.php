<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $table = 'log_user';

    protected $appends = ['privilege_code'];

    protected $fillable = [
        'username',
        'privilege_id',
        'description',
        'ip_address',
        'agent',
    ];

    public function privilege() {
        return $this->hasOne(Privilege::class, 'id', 'privilege_id');
    }
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d M Y H:i:s');
    }

    public function getPrivilegeCodeAttribute()
    {
        $staticCode = array_combine(config('global.privilege.static.id'), config('global.privilege.static.code'));

        if(in_array($this->privilege_id, config('global.privilege.static.id'))) {
            $code = $staticCode[$this->privilege_id];
        } else {
            $code = self::find($this->id)->privilege()->first()->code;
        }

        return $code;
    }
}
