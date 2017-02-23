<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_token';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['token', 'expires_at'];

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}