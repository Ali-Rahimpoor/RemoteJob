<?php
function api_post_job(){
   $is_error = false;
   $message = [];
   is_login();
   $title = requestInput('title','POST');
   if(!$title){
      $is_error = true;
      $message[] = 'عنوان نباید خالی بمونه';
   }
   $excerpt = requestInput('excerpt','POST');
   if(!$excerpt){
      $is_error = true;
      $message[] = 'خلاصه پروژه نباید خالی بمونه';
   }
   $salary = valid_salary(requestInput('salary','POST'));
   if(!$salary){
      $is_error = true;
      $message[] = 'قیمت گزاری معتبر نمیباشد';
   }
   $description = autop(requestInput('description','POST'));
   if(!$description){
      $is_error = true;
      $message[] = 'توضیحات نباید خالی بمونه';
   }
   $duration = valid_duration(requestInput('duration','POST'));
   if(!$duration){
      $is_error = true;
      $message[] = 'مدت زمان ورودی معتبر نمیباشد';
   }
   $user_id = get_current_user_id();
   $status = 'pending';
   $created = current_time();
   $expire = date('Y-m-d H:i:s', strtotime(current_time() . ' +1 month'));
   if($is_error){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است',
         'data' => $message
      ],400);
   }
   $data = [
      'title' => $title,
      'excerpt'=> $excerpt,
      'salary'=>$salary,
      'description'=> $description,
      'duration'=> $duration,
      'user_id'=>$user_id,
      'status'=>$status,
      'created_at'=>$created,
      'expire_at'=> $expire
   ];
   $job_id = db_insert('jobs',$data);
   if(!$job_id){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است'
      ],500);
   }
   send_json([
      'success' => true,
      'message' => 'بعد از بررسی کارشناسان ما پروژه شما منتشر خواهد شد',      
      'expire' => $expire
   ],201);
}
function api_get_jobs(){
   $where = " status = 'publish' ";
   
   $jobs = db_fetch_all(" SELECT * FROM jobs WHERE $where ");
   if(!$jobs){
      send_json([
         'success' => false,
         'message' => 'کاری پیدا نشد'
      ],404);
   }
   send_json([
      'success' => true,
      'message' => 'لیست کارها پیدا شد',
      'data'    => $jobs
   ],200);
}
function api_put_job(){
   is_admin();
   $is_error = false;
   $message = [];
   $job_id = requestInput('job_id',"POST");
   if(!$job_id){
      $is_error = true;
      $message[] = 'شغلی که باید تغییر وضعیت کنه پیدا نشد';
   }
   $status = valid_status(requestInput('status',"POST"));
   if(!$status){
      $is_error = true;
      $message[] = 'وضعیت مورد قبول نیست';
   }
   if($is_error){
      send_json([
         'success' => false,
         'message' => $message
      ],404);
   }
   $update_job = db_update('jobs',[
      'status' => $status,
   ],[
      'ID' => $job_id
   ]);
   if($update_job){
      send_json([
         'success' => true,
         'message' => 'وضعیت تغییر کرد'
      ],200);
   }
   send_json([
      'success' => false,
      'message' => 'هیچ تغییری ایجاد نشد'
   ],200);
}