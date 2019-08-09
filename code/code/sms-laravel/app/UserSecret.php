<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserSecret extends Model
{
    protected $guarded = [];

    static function saveSecret($username, $secretName, $encryptedSecret){
        $userkeyDetails = UserKey::where('username',$username)->first();
        if(empty($userkeyDetails)) throw new \Exception('Username is invalid or not registed');

        $userPubKey = $userkeyDetails->public_key;

        $serverPrivateKey = Storage::get('private.key');
       
        openssl_private_decrypt($encryptedSecret, $orgmsg, $serverPrivateKey);
        if(empty($orgmsg)){
            throw new Exception('Message cannot be decrypted');    
        }

        openssl_public_encrypt($orgmsg, $enc, $userPubKey);
           
        $model = new self();
        $model->insert([
            'username' => $username,
            'secret_name' => $secretName,
            'encrypted_secret' => base64_encode($enc)
        ]);
    }
}
