<?php
const USER_ROLES = [   
   'employer',
   'job_seeker'
];

function create_api_key($user_id){
    $api_key = generate_random_string(30);
    $expire = date('Y-m-d H:i:s', strtotime(current_time() . ' +1 month'));
    $data = [
        'user_id'   => $user_id,
        'api_key'   => $api_key,
        'status'    => 'active',
        'ip'        => $_SERVER['REMOTE_ADDR'],
        'created_at'=> current_time(),
        'updated_at'=> current_time(),
        'last_used_at'=> current_time(),
        'expire_at' => $expire
    ];
    $inserted = db_insert('application_api_keys',$data);
    if($inserted){
        return $api_key;
    }
    return false;
}
function is_login(){
    global $current_user;
    if($current_user){
        $is_login = $current_user['ID'] ? true : false;
        if(!$is_login) {
            send_json([
                'success' => false,
                'message' => 'نیاز به احراز هویت'
            ],401);
        }
    }else{
        send_json([
            'success' => false,
            'message' => 'نیاز به احراز هویت'
        ],401);
    }
}
function get_current_user_id(){
    is_login();
    global $current_user;
    return $current_user['ID'];
}
function get_current_user_info(){
    is_login();
    global $current_user;
    $allowedData =[
        'id' => $current_user['ID'],
        'full_name' => $current_user['full_name'],
        'role' => $current_user['role'],
        'user_name' => $current_user['user_name'],
        'avatar' =>  site_url($current_user['avatar']),
        'phone' => $current_user['phone'],
        'email' => $current_user['email'],
        'birthdate' => $current_user['birthdate']
    ];
    return $allowedData;
}
function get_current_user_role($need_login=true){    
    if($need_login){
        is_login();
    }
    global $current_user;
    if($current_user){
        return $current_user['role'];
    }
}
function is_admin(){
    $role = get_current_user_role();
    $is_admin = $role === 'admin'   ? true  : false ;
    if(!$is_admin){
        send_json([
            'success' => false,
            'message' => 'دسترسی غیرمجاز'
        ],403);
    }
}