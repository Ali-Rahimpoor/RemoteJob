<?php
function api_login_user(){
  $user_name = requestInput('user_name');
  $password = md5(requestInput('password'));

  $res = db_query(" SELECT * FROM users WHERE user_name = '$user_name' AND password = '$password' ");
  if($res && $res -> num_rows){
    $user_id = mysqli_fetch_assoc($res)['ID'];
    $api_key = create_api_key($user_id);
    if(!$api_key){
      send_json([
        'success' => false,
        'message' => 'مشکلی پیش آمده است'
      ],500);
    }
    send_json([
      'success' => true,
      'message' => 'خوش آمدید',      
      'api_key' => $api_key
    ],200);
  }
  send_json([
    'success' => false,
    'message' => 'نام کاربری یا رمز عبور اشتباه هست',
  ],404);
}
function api_register_request(){
  $user_name = (requestInput('user_name'));
  $user_role = (requestInput('user_role'));   
  $password = (requestInput('password'));
  $full_name = requestInput('full_name');  
  $phone = requestInput('phone',null);  
  $email = requestInput('email',null);  
  $birthdate = requestInput('birthdate',null);
  
  $errors = [];
  // INPUT Validation
  if(!check_username_is_en($user_name) ){
    $errors[] = 'نام کاربری باید انگلیسی باشد';
  }
  if(!check_username_length($user_name)){
    $errors[] = 'نام کاربری باید بیشتر از 3 کلمه باشد';
  }
  if(!check_user_role($user_role)){
    $errors[] = 'این نقش کاربری وجود ندارد';
  }
  if(!check_password_length($password)){
    $errors[] = 'پسوورد باید بیشتر از 6 کلمه باشد';
  }
  if(!validate_password($password)){
    $errors[] = 'پسورد باید شامل اعداد و حروف باشد';
  }
  if(!validate_fullname($full_name)){
    $errors[] = 'نام و نام خانوادگی خود را وارد کنید';
  }
  if(!validate_phone($phone,true)){
    $errors[] = 'شماره تلفن معتبر وارد کنید';
  }
  if(!validate_email($email,true)){
    $errors[] = 'ایمیل معتبر وارد کنید';
  }
  if(!validate_birthdate($birthdate,true)){
    $errors[] = 'تاریخ تولد معتبر وارد کنید';
  }
  // END INPUT Validation
  if (!empty($errors)) {
    send_json([
        'success' => false,
        'message' => 'اطلاعات ارسالی معتبر نیست.',
        'errors' => $errors
    ], 422);
  }

  // DATABASE Validation
  if(!check_exist_user_name($user_name)){
    $errors[] = 'این نام کاربری از قبل وجود دارد';
  }
  if(!check_phone_exists($phone,true)){
    $errors[] = 'این شماره تلفن در سایت وجود دارد';
  }
  if(!check_email_exists($email,true)){
    $errors[] = 'این ایمیل از قبل وجود دارد';
  }
  
  // END DATABASE Validation
  if (!empty($errors)) {
    send_json([
        'success' => false,
        'message' => 'اطلاعات ارسالی معتبر نیست.',
        'errors' => $errors
    ], 422);
  }   
  $password = md5($password);
  $data = [
   'user_name' => $user_name,
   'role'=> $user_role,
   'password' => $password,
   'full_name'=>$full_name,
   'phone' => $phone,
   'email' => $email,
   'birthdate'=> $birthdate,
   'updated_at' => current_time(),
   'created_at' => current_time(),
  ];
  $user_id = db_insert('users',$data);
  if(!$user_id){
    send_json([
      'success' => false,
      'message' => 'خطایی رخ داده است'
    ],500);
  }
  unset($data['password']);
  send_json([
    'success' => true,
    'message' => 'شما با موفقیت ثبت نام شدید',
    'data'    => $data
  ],201);
  
}
function api_upload_avatar(){
  is_login();
  $user_id = get_current_user_id();
  if(!$user_id){
    send_json([
      'successs' => false,
      'message' => 'این کاربر وجود ندارد'
    ],404);
  }
  $user_name = db_fetch_column("SELECT user_name from users WHERE ID = '$user_id' LIMIT 1");
  if(!$user_name){
    send_json([
      'success' => false,
      'message' => 'این کاربر یافت نشد'
    ],404);
  }
  $file = isset($_FILES['user_avatar']) ? $_FILES['user_avatar'] : false;
  if(!$file){
    send_json([
      'success' => false,
      'message' => 'فایلی دریافت نشد'
    ],404);
  }
  $uploaded_image = uploadImage($file,$user_name);
  if(!$uploaded_image['success']){
    send_json($uploaded_image,$uploaded_image['response']);
  }
  db_update('users',[
    'avatar' => $uploaded_image['filepath'],
    'updated_at' => current_time()
  ],[
    'ID' => $user_id
  ]);
  $data = [
    'success' => true,
    'message' => 'تصویر با موفقیت جایگزین شد',
    'avatar'  => site_url($uploaded_image['filepath']),
    'file'    => $uploaded_image,
  ];
  send_json($data,201);
}
function api_user_info(){
  is_login();
  $res = get_current_user_info();
  if(!$res){
    send_json([
      'success' => false,
      'message' => 'خطایی رخ داده است',
    ],500);
  }
  send_json([
    'success' => true,
    'message' => 'اطلاعات با موفقیت دریافت شد',
    'data' => $res
  ],200);
}