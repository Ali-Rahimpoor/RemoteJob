<?php
function send_json($data,$status){
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($status);
    
    $json = json_encode($data);
    if ($json === false) {
        http_response_code(500);
        echo json_encode(
         [
            'success' => false,
            'message' => 'failed to encode data to json'
         ]
         );
        exit;
    }
    
    echo $json;
    exit;
}
function redirect($url){
   header("Location:$url");
   exit;
}
function generate_random_string($len=10){
   $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmopqrstuvwxyz';
   $result ='';
   for ($i=0; $i <= $len ; $i++) { 
         $random = rand(0,strlen($chars) -1);
         $result .=  $chars[$random];
   }
   return $result;
}
function current_time(){
    return date('Y-m-d H:i:s');
}
function site_url($path=''){
    return SITE_URL . $path;
}
function second_to_time($sec){
 $m = floor($sec/60)   ;
 $s = $sec %60;
 if($m < 10){
    $m = '0'.$m;
 }
 if($s < 10){
    $s = '0'.$s;
 }
 return "$m:$s";
}
function autop($content){
    $content = str_replace(['\\r\\n','\\n'],PHP_EOL,$content);
    $content_lines = explode(PHP_EOL,$content);
    return '<p>' . implode('<p></p>',$content_lines) . '</p>';
}
function auto_number_data($array){
     if ($array === null || !is_array($array)) {
        return []; 
    }
    if(array_is_list($array)){
        $array = array_map(function ($item) {
            foreach ($item as $key => $value) {                     
                if(is_string($value) && ctype_digit($value)){
                    $item[$key] = (int) $value;
                }
            }
            return $item;
        },$array);
    }else{
          foreach ($array as $key => $value) {                     
                if(is_string($value) && ctype_digit($value)){
                    $array[$key] = (int) $value;
                }
            }
    }
    return $array;
}
function is_api_request(){
    return defined('IS_API_REQUEST');
}
function get_api_key(){
    $headers = getallheaders();
    return isset($headers['X-Api-Key']) ? isset($headers['X-Api-Key']) : false;
}
// ??
function get_api_input($key,$default=null){
    global $api_inputs;
    return isset($api_inputs[$key]) ? $api_inputs[$key] : $default;
}