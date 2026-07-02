<?php
function api_post_skill(){
    is_admin();

    $skill_name = strtolower(trim(requestInput('skill_name')));

    if ($skill_name === '') {
        send_json([
            'success' => false,
            'message' => 'نام مهارت الزامی است.'
        ], 422);
    }

    // بررسی وجود مهارت
    $skill = db_fetch_column("SELECT id FROM skills WHERE name = '$skill_name' LIMIT 1");

    if ($skill) {
        send_json([
            'success' => false,
            'message' => 'این مهارت قبلاً ثبت شده است.'
        ], 409);
    }

    $skill_id = db_insert('skills', [
        'name' => $skill_name
    ]);

    if (!$skill_id) {
        send_json([
            'success' => false,
            'message' => 'خطایی رخ داد.'
        ], 500);
    }

    send_json([
        'success' => true,
        'message' => 'مهارت با موفقیت اضافه شد.',
        'id'      => $skill_id
    ], 201);
}
function api_get_skills(){
    $skills = db_fetch_all("SELECT * FROM skills");
    if($skills){
        send_json([
            'success' => true,
            'message' => 'مهارت ها دریافت شدند',
            'data'    => $skills
        ],200);
    }
}