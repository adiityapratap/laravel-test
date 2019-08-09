<?php

require_once "ApiHandler.php";
require_once "config.php";

echo "Avaliable Commands: register, getServerKey, storeSecret, getSecret quit \n";

while(true){
    echo "Enter command: \n";
    $input = trim(fgets(STDIN, 1024));
    echo "Execute Command: $input \n";

    switch($input){
        case "register": 
            register();
            echo "\n";
            break;

        case "getServerKey": 
            getServerKey();
            echo "\n";
            break;

        case "storeSecret": 
            storeSecret();
            echo "\n";
            break;

        case "getSecret": 
            getSecret();
            echo "\n";
            break;

        case "quit":
            exit; 
            break;

        default:
            echo "Invalid command. Please try again. \n";
    }
}

function register(){
    echo "Enter Username:\n";
    $usernm = trim(fgets(STDIN, 1024));

    if(file_exists("{$usernm}_private.key")){
        echo "User already exists:";
        return;
    }
    $keys = generateKey();
    
    file_put_contents("{$usernm}_private.key", $keys['priv']);
    file_put_contents("{$usernm}_public.key", $keys['pub']);
    sendRegisterRequest($usernm, $keys['pub']);
}

function generateKey(){
    $res = openssl_pkey_new();
    openssl_error_string(); 
    openssl_pkey_export($res, $privKey);
    openssl_error_string(); 

    $pubKey = openssl_pkey_get_details($res);
    $pubKey = $pubKey["key"];

    return ['pub' => $pubKey, 'priv' => $privKey];
}

function sendRegisterRequest($usernm, $privKey){
    $api = new ApiHandler;
    $api->sendCurlPostRaw(API_URL . "/register/$usernm", $privKey);
}

function getServerKey(){
    $api = new ApiHandler;
    $data = $api->sendCurlGet(API_URL . "/getServerKey");
    file_put_contents("server_public.key", $data);
}

function storeSecret(){
    echo "Enter Username: \n";
    $username = trim(fgets(STDIN, 1024));
    echo "Enter SecretName: \n";
    $secretName = trim(fgets(STDIN, 1024));
    echo "Enter Message: \n";
    $message = trim(fgets(STDIN, 1024));

    $serverKey = getServerKeyLocal();
    openssl_public_encrypt($message, $encryptedSecret, $serverKey);
    $api = new ApiHandler;
    echo $api->sendCurlPostRaw(API_URL . "/storeSecret/$username/$secretName", $encryptedSecret);
}

function getServerKeyLocal(){
    if(!file_exists("server_public.key")){
        getServerKey();
    }
    return file_get_contents("server_public.key");
}

function getSecret(){
    echo "Enter Username: \n";
    $username = trim(fgets(STDIN, 1024));
    echo "Enter SecretName: \n";
    $secretName = trim(fgets(STDIN, 1024));

    $api = new ApiHandler;
    $msg = $api->sendCurlGet(API_URL . "/getSecret/$username/$secretName");

    $key = file_get_contents("{$username}_private.key");

    openssl_private_decrypt($msg, $orgmsg, $key);

    echo $orgmsg;
    echo "\n";
}
