<?php
// JOBS
get('/jobs/','api_get_jobs');


// AUTH
post('/user/register/','api_register_request');
post('user/change_avatar/{user_id}','api_upload_avatar');
post('/user/login','api_login_user');