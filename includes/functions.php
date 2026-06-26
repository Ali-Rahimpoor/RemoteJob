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
function requestInput($var,$method="GET",$else_arg=false){
   if($method == "GET"){
        return isset($_GET[$var]) && $_GET[$var] ? db_escape($_GET[$var]) : $else_arg;
   }
   elseif ($method == "POST"){
        return isset($_POST[$var]) && $_POST[$var] ? db_escape($_POST[$var]) : $else_arg;
   }
   else{
        return false;
   }
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
function uploadImage($file, $prefix, $baseDir = "uploads/", $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'], $maxSize = 5242880) {
    
    // ========== ساختار پوشه سال/ماه ==========
    $year = date('Y');
    $month = date('m');
    $targetDir = $baseDir . $year . '/' . $month . '/';
    
    // ایجاد پوشه اگر وجود نداشته باشد
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // بررسی خطاهای آپلود
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'خطا در آپلود فایل: ' . $file['error'],
            'response' => 400
        ];
    }

    // بررسی نوع فایل
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'نوع فایل مجاز نیست. فقط: ' . implode(', ', $allowedTypes),
            'response' => 400
        ];
    }

    // بررسی سایز فایل
    if ($file['size'] > $maxSize) {
        return [
            'success' => false,
            'message' => 'حجم فایل بیشتر از حد مجاز است. حداکثر: ' . ($maxSize / 1024 / 1024) . 'MB',
            'response' => 400
        ];
    }

    // ========== تغییرات اعمال شده ==========
    // حفظ نام اصلی فایل با اضافه کردن پیشوند
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = $prefix . '-' . $originalName . '.' . $extension;
    $targetPath = $targetDir . $newFileName;

    // اگر فایل با همین نام وجود داشت، حذفش کن
    if (file_exists($targetPath)) {
        unlink($targetPath);
    }

    // انتقال فایل به پوشه مقصد
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'message' => 'تصویر با موفقیت آپلود شد',
            'filename' => $newFileName,
            'filepath' => $targetPath,           
            'filesize' => $file['size'],            
            'response' => 201
        ];
    } else {
        return [
            'success' => false,
            'message' => 'خطا در ذخیره فایل',
            'response' => 500
        ];
    }
}
function setup_api_user(){
    $headers = getallheaders();
    if(isset($headers['x-api-key'])){
        $api_key = db_escape($headers['x-api-key']);
        $sql = "SELECT * FROM application_api_keys WHERE api_key = '$api_key' AND status = 'active'";
        $api = db_fetch_assoc($sql);
        if($api){
            $user_id = $api['user_id'];
            $user = db_fetch_assoc_by('users','ID',$user_id);
            if($user){
                $GLOBALS['current_user'] = auto_number_data($user);    
                // add last use
                db_update('application_api_keys',[
                    'last_used_at' => current_time()
                ],[
                    'ID' => $api['ID']
                ]);
            }
        }
    }
}