<?php
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
function validate_phone($phone,$nullable=false){
    if($nullable){
        if($phone === null){
            return true;
        }
    }
    $phone = trim($phone);

    if (preg_match('/^09\d{9}$/', $phone)) {
        return $phone;
    }

    return false;
}
function check_phone_exists($phone,$nullable=false){
    if($nullable){
        if($phone === null){
            return true;
        }
    }
    $res = db_query("SELECT id FROM users WHERE phone = '$phone' LIMIT 1");

    if ($res && $res->num_rows > 0) {
        return false;
    }

    return $phone;
}
function validate_birthdate($birthdate,$nullable=false){
    if($nullable){
        if($birthdate === null){
            return true;
        }
    }
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
function validate_email($email,$nullable=false){
    if($nullable){
        if($email === null){
            return true;
        }
    }
    $email = trim($email);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }

    return false;
}
function check_email_exists($email,$nullable=false){
    if($nullable){
        if($email === null){
            return true;
        }
    }
    $res = db_query("SELECT id FROM users WHERE email = '$email' LIMIT 1");

    if ($res && $res->num_rows > 0) {
        return false;
    }

    return $email;
}
function check_username_is_en($username){
    $username = trim($username);

    if (preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return $username;
    }

    return false;
}
function check_username_length($username){
    $username = trim($username);
    $length = strlen($username);
    if ($length >= 3 && $length <= 30) {
        return $username;
    }
    return false;
}
function check_password_length($password){
    $password = trim($password);

    $length = strlen($password);

    if ($length >= 8 && $length <= 64) {
        return $password;
    }

    return false;
}
function valid_salary($salary){
   if(array_key_exists($salary,JOBS_SALARAIES)){
      return $salary;
   }
      return false;
}
function valid_duration($duratoin){
   if(array_key_exists($duratoin,JOBS_DURATIONS)){
      return $duratoin;
   }
      return  false;
}
function valid_status($status){
   if(array_key_exists($status,JOBS_STATUSES)){
      return $status;
   }
   return false;
}
function valid_skills($skills){
    if (!is_array($skills) || empty($skills)) {
        return false;
    }

    $ids = implode(',', $skills);

    $count = db_fetch_column("
        SELECT COUNT(*)
        FROM skills
        WHERE id IN ($ids)
    ");

    if ($count != count($skills)) {
        return false;
    }

    return $skills;
}
