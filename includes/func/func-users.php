<?php
const USER_ROLES = [   
   'employer',
   'job_seeker'
];
function check_exist_user_name($user_name){
   $res = db_query("SELECT ID FROM users WHERE user_name = '$user_name' LIMIT 1");
   if($res && $res -> num_rows){
      return false;
   }
   return $user_name;
}
function check_user_role($user_role){
   if(in_array($user_role,USER_ROLES,true)){
      return $user_role; 
   }
   return false;
}
function validate_password($password){
    if (
        strlen($password) >= 6 &&
        preg_match('/[A-Za-z]/', $password) &&
        preg_match('/[0-9]/', $password)
    ) {
        return $password;
    }

    return false;
}
function validate_phone($phone){
    if (preg_match('/^09\d{9}$/', $phone)) {
        $res = db_query(" SELECT ID FROM users WHERE phone = '$phone' LIMIT 1 ");
        if($res && $res -> num_rows){
         return false;
        }
        return $phone;
    }

    return false;
}
function validate_birthdate($birthdate){
    $date = DateTime::createFromFormat(
        'Y-m-d',
        $birthdate
    );

    if (
        $date &&
        $date->format('Y-m-d') === $birthdate
    ) {
        return $birthdate;
    }

    return false;
}
function validate_fullname($fullname){
    $fullname = trim($fullname);

    if (
        preg_match('/^[آ-ی\s]+$/u', $fullname) &&
        mb_strlen($fullname) >= 3
    ) {
        return $fullname;
    }

    return false;
}
function validate_email($email){    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $res = db_query("SELECT ID FROM users WHERE email = '$email' LIMIT 1");
        if($res && $res -> num_rows){
         return false;
        }
        return $email;
    }

    return false;
}
function create_api_key($user_id){
    $api_key = generate_random_string(30);
    $data = [
        'user_id'   => $user_id,
        'api_key'   => $api_key,
        'status'    => 'active',
        'ip'        => $_SERVER['REMOTE_ADDR'],
        'created_at'=> current_time(),
        'updated_at'=> current_time(),
        'last_used_at'=> current_time()
    ];
    $inserted = db_insert('application_api_keys',$data);
    if($inserted){
        return $api_key;
    }
    return false;
}
function is_login(){
    global $current_user;
    $is_login = $current_user['ID'] ? true : false;
    if(!$is_login) {
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
        'avatar' => $current_user['avatar'],
        'phone' => $current_user['phone'],
        'email' => $current_user['email'],
        'birthdate' => $current_user['birthdate']
    ];
    return $allowedData;
}