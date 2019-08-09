<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserKey extends Model
{
    static function saveUser($username, $key){

        $model = new self();
        $model->username = $username;
        $model->public_key = $key;
        $model->save();
    }
}
