<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\UserKey;
use App\UserSecret;

class SmsController extends Controller{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register($username){
        $data = $this->request->getContent();
        try{
            UserKey::saveUser($username, $data);
            return response('success', 200);
        }catch(\Exception $e){
            \Log::alert($e);
            dd($e->getMessage());
        }
    }

    public function getServerKey(){
        if(!Storage::exists('private.key')){
            $res = openssl_pkey_new();
            openssl_error_string(); 
            openssl_pkey_export($res, $privKey);
            openssl_error_string(); 
        
            $pubKey = openssl_pkey_get_details($res);
            $pubKey = $pubKey["key"];
            Storage::put('private.key', $privKey);
            Storage::put('public.key', $pubKey);
        }else{
            $pubKey = Storage::get('public.key');
        }
                      
        return response($pubKey, 200);
    }

    public function storeSecret($username, $secretName){
        try{
            UserSecret::saveSecret($username, $secretName, $this->request->getContent());        
            return response("Secret Saved Successfully", 200);      
        }catch(\Exception $e){
            \Log::alert($e->getMessage());
            return response("Error saving secret", 400);    
        }
    }

    public function getSecret($username, $secretName){
        try{
            $secret = UserSecret::where(['username' => $username, 'secret_name' => $secretName])->first();   
            if(empty($secret)) throw new \Exception('Secret not found');
            return response(base64_decode($secret->encrypted_secret, 200));      
        }catch(\Exception $e){
            \Log::alert($e->getMessage());
            return response("Error getting secret", 400);    
        }
    }    
}