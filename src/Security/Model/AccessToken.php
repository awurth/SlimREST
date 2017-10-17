<?php

namespace App\Security\Model;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'access_token';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['token', 'expires_at'];

    public function user()
    {
        return $this->belongsTo('App\Security\Model\User');
    }
}
