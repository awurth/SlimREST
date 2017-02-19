<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'access_token';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['token'];

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}