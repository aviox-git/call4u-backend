<?php

namespace App\Models;

use Auth;
use Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Model;

class Admin extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function getAuthPassword()
    {
        return $this->password;
    }

    public static function login($request)
    {
        $requestData = $request->all();

        if (Auth::guard('admin')->attempt(['email' => $requestData['email'], 'password' => $requestData['password']]))
            return TRUE;

        return FALSE;
    }
}
