<?php
function api_post_job(){      
   is_login();
   $erros = [];
   $title = requestInput('title');
   if(!$title){
      $erros[] = 'عنوان نباید خالی بمونه';
   }
   $excerpt = requestInput('excerpt');
   if(!$excerpt){      
      $erros[] = 'خلاصه پروژه نباید خالی بمونه';
   }
   $salary = valid_salary(requestInput('salary'));
   if(!$salary){      
      $erros[] = 'قیمت گزاری معتبر نمیباشد';
   }
   $description = autop(requestInput('description'));
   if(!$description){      
      $erros[] = 'توضیحات نباید خالی بمونه';
   }
   $duration = valid_duration(requestInput('duration'));
   if(!$duration){      
      $erros[] = 'مدت زمان ورودی معتبر نمیباشد';
   }
   $skills = valid_skills(requestInput('skills'));   
   if(!$skills){
      $erros[] = 'مهارت نامعتبر میباشد';
   }
   
   $user_id = get_current_user_id();
   $status = 'pending';
   $created = current_time();
   $expire = date('Y-m-d H:i:s', strtotime(current_time() . ' +1 month'));
   $slug = generate_random_string(6,'1234567890');
   if(!empty($erros)){
      send_json([
         'success' => false,
         'message' => 'خطایی رخ داده است',
         'erros' => $erros
      ],422);
   }
   $data = [
      'title' => $title,
      'slug' => $slug,
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
   foreach($skills as $skill){
      db_insert('job_skills',[
         'job_id' => $job_id,
         'skill_id' => $skill
      ]);
   }
   send_json([
      'success' => true,
      'message' => 'بعد از بررسی کارشناسان ما پروژه شما منتشر خواهد شد',      
      'expire' => $expire
   ],201);
}
function api_get_jobs(){
   $user_role = get_current_user_role(false);
   $status = requestInput('status','');
   $search = requestInput('search','');
   $salary = requestInput('salary','');
   $duration = requestInput('duration','');
   $skills = requestInput('skills','');
   $order = requestInput('order','date');
   $order_by = requestInput('order_by','newest');
   $where = " 1 = 1 ";
   // STATUS
   if($user_role != 'admin'){
      $where .= " AND status = 'publish' ";
   }   
   if(!empty($status) && $user_role == 'admin' && array_key_exists($status,JOBS_STATUSES)){
      $where .= " AND status = '$status' ";
   }
   // SEARCH
   if(!empty($search)){
      $where .= " AND  (title LIKE '%$search%' OR description LIKE '%$search%' )";
   }
   // SALARY
   if(!empty($salary) && array_key_exists($salary,JOBS_SALARAIES)){
      $where .= " AND salary = '$salary' ";
   }
   // DURATION
   if(!empty($duration) && array_key_exists($duration,JOBS_DURATIONS)){
      $where .= " AND duration = '$duration' ";
   }
   
   // ORDER
   if(!empty($order) && array_key_exists($order,JOBS_ORDER)){
      $order = JOBS_ORDER[$order];
   }
   // ORDER BY
   if(!empty($order_by) && array_key_exists($order_by,JOBS_SORT)){
      $order_by = JOBS_SORT[$order_by];
   } 

   // PAGINATE
   $page = (int) requestInput('page',1);
   $per_page = JOBS_PER_PAGE;
   $offset = ($page - 1) * $per_page;
   
   $jobs_sql = " SELECT * FROM 
      view_jobs 
      WHERE $where 
      ORDER BY $order $order_by
      LIMIT $per_page OFFSET $offset";
      
   $rows = db_fetch_all($jobs_sql);   
   if (!$rows) {
    send_json([
        'success' => false,
        'message' => 'کاری پیدا نشد'
    ], 404);
   }
   $jobs = [];
   foreach($rows as $row){
      $id = $row['ID'];
      if(!isset($jobs[$id])){
         $jobs[$id] =[
            'ID' => $row['ID'],
            'slug' => $row['slug'],
            'title' => $row['title'],
            'status' => $row['status'],
            'min_score'=> $row['min_score'],
            'excerpt' => $row['excerpt'],
            'cover_url' => get_cover_url($row['cover_url']),
            'duration' => $row['duration'],
            'salary' => $row['salary'],
            'created_at' => $row['created_at'],
            'skills' => [],
            'user_fullname' => $row['user_fullname'],
            'user_avatar' => get_avatar_url($row['user_avatar']),
            'user_score' => $row['user_score']
        ];
      }
      $jobs[$id]['skills'][]=[
         'id' => $row['skill_id'],
         'name'=> $row['skill_name']
      ];
   }
   $jobs = auto_number_data(array_values($jobs));   
   $total  = db_fetch_column(" SELECT COUNT(DISTINCT ID) AS total FROM view_jobs WHERE $where ");
   

   if(!$jobs){
      send_json([
         'success' => false,
         'message' => 'کاری پیدا نشد'
      ],404);
   }
   send_json([
      'success' => true,
      'message' => 'لیست کارها پیدا شد',
      'data'    => $jobs,
      'pagination' => auto_number_data([
         'current_page' => $page,
         'per_page' => $per_page,
         'total' => $total,
         'total_pages' => ceil($total/$per_page)
      ])
   ],200);
}
function api_put_job(){
   is_admin();
   $is_error = false;
   $message = [];
   $job_id =  requestInput('job_id');
   if(!$job_id){
      $is_error = true;
      $message[] = 'شغلی که باید تغییر وضعیت کنه پیدا نشد';
   }
   $status = valid_status(requestInput('status'));
   if(!$status){
      $is_error = true;
      $message[] = 'وضعیت مورد قبول نیست';
   }
   if($is_error){
      send_json([
         'success' => false,
         'message' => $message
      ],422);
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