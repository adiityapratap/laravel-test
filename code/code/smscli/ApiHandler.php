<?php 

class ApiHandler{
    public function sendCurlPost($url, $data){
        
        $fields_string = "";
        foreach($data as $key=>$value) { $fields_string .= $key.'='. $value .'&'; }
        $fields_string = rtrim($fields_string, '&');
        
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    public function sendCurlGet($url){
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output=curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function sendCurlPostRaw($url, $msg){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch,CURLOPT_POSTFIELDS, $msg);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }
}