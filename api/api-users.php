<?php
function api_login_user(){
  $user_name = requestInput('user_name','POST');
  $password = md5(requestInput('password',"POST"));

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
   // VALIDATION
  $user_name = check_exist_user_name((requestInput('user_name',"POST")));
  if(!$user_name){
    send_json([
      'success' => false,
      'message' => 'نام کاربری از قبل وجود دارد',      
    ],404);
  }
  $user_role = check_user_role((requestInput('user_role',"POST"))); 
  if(!$user_role){
    send_json([
      'success' => false,
      'message' => 'این نقش کاربری وجود ندارد',
      
    ],404);
  }
  $password = validate_password(requestInput('password','POST'));
  if(!$password){
    send_json( [
      'success' => false,
      'message' => 'پسورد باید شامل اعداد و حروف باشد',
    ],404);
  }
  $full_name = validate_fullname(requestInput('full_name',"POST"));
  if(!$full_name){
    send_json([
      'success' => false,
      'message' => 'نام خود را خالی نزارید و اسم باید فارسی باشه',
    ],404);

  }
  $phone = validate_phone(requestInput('phone',"POST",null));
  if(!$phone){
    send_json([
      'success' => false,
      'message' => 'شماره موبایل از قبل وجود دارد',
    ],404);
  }
  $email = validate_email(requestInput('email',"POST",null));
  if(!$email){
    send_json([
      'success' => false,
      'message' => 'ایمیل معتبر نیست'
    ],404);
  }
  $birthdate = validate_birthdate(requestInput('birthdate',"POST",null));
  if(!$birthdate){
    send_json([
      'success' => false,
      'message' => 'تاریخ تولد معتبر نیست'
    ],404);
  }
  $password = md5($password);
  $data = [
   'user_name' => $user_name,
   'password' => $password,
   'full_name'=>$full_name,
   'phone' => $phone,
   'email' => $email,
   'birthdate'=> $birthdate,
   'role'=> $user_role,
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
  send_json([
    'success' => true,
    'message' => 'شما با موفقیت ثبت نام شدید',
    'data'    => [
      'user_id' => $user_id
    ]
  ],201);
  
}
function api_upload_avatar($user_id){
  if(!$user_id){
    send_json([
      'success' => false,
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
  $file_upload = uploadImage($file,$user_name);
  if(!$file_upload['success']){
    send_json($file_upload,$file_upload['response']);
  }
  db_update('users',[
    'avatar' => $file_upload['filepath'],
    'updated_at' => current_time()
  ],[
    'ID' => $user_id
  ]);
  $data = [
    'success' => true,
    'message' => 'تصویر با موفقیت جایگزین شد',
    'avatar'  => ABSPATH . $file_upload['filepath'],
    'file'    => $file_upload,
  ];
  send_json($data,201);

}