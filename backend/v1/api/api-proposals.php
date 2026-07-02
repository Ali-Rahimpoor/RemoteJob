<?php
function api_post_proposal($slug){
   is_login();
   $errors = [];
   $proposal = requestInput('proposal');
   if (!$proposal) {
    $errors[] = 'متن پروپوزال الزامی است.';
   } elseif (mb_strlen($proposal) < 5) {
      $errors[] = 'متن پروپوزال باید حداقل 5 کاراکتر باشد.';
   } elseif (mb_strlen($proposal) > 5000) {
      $errors[] = 'متن پروپوزال نباید بیشتر از ۵۰۰۰ کاراکتر باشد.';
   }
   $salary = requestInput('salary');
   if ($salary === null || $salary === '') {
    $errors[] = 'مبلغ پیشنهادی الزامی است.';
   } elseif (!is_numeric($salary)) {
      $errors[] = 'مبلغ پیشنهادی نامعتبر است.';
   } elseif ($salary <= 0) {
      $errors[] = 'مبلغ پیشنهادی باید بیشتر از صفر باشد.';
   }
   $duration = requestInput('duration');
   if ($duration === null || $duration === '') {
    $errors[] = 'مدت زمان انجام پروژه الزامی است.';
   } elseif (!ctype_digit((string)$duration)) {
      $errors[] = 'مدت زمان نامعتبر است.';
   } elseif ($duration < 1 || $duration > 365) {
      $errors[] = 'مدت زمان باید بین ۱ تا ۳۶۵ روز باشد.';
   }

   if(!empty($errors)){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است',
         'errors'  => $errors
      ],422);
   }
   $user_id = get_current_user_id();
   $job = get_job_from_slug($slug);
   if(!$job){
      $errors[] = 'آدرس نامعتبر میباشد';
   }
   $job_id = $job['ID'];
   if ($job['status'] !== 'publish') {
      $errors[] = 'امکان ارسال پروپوزال برای این پروژه وجود ندارد.';
   }
   if ($job['user_id'] == $user_id) {
    $errors[] = 'نمی‌توانید برای پروژه خودتان پروپوزال ارسال کنید.';
   }
   if (user_has_proposal($job_id, $user_id)) {
    $errors[] = 'شما قبلاً برای این پروژه پروپوزال ارسال کرده‌اید.';
   }

   
   
   
   if(!empty($errors)){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است',
         'errors'  => $errors
      ],422);
   }

   $data = auto_number_data([
      'job_id' => $job_id,
      'user_id' => $user_id,
      'proposal' => $proposal,
      'salary' => $salary,
      'duration' => $duration,
      'created_at' => current_time()
   ]);
   
   $proposal_id = db_insert('job_applications',$data);
   if(!$proposal_id){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است',
      ],500);
   }
   send_json([
      'success' => true,
      'message' => 'پروپوزال با موفقیت ارسال شد',
      'data' => $proposal_id
   ],201);
   
}
