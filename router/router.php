<?php
// JOBS
get('/jobs/','api_get_jobs');

// USERS
get('user/profile','api_user_info');
post('user/change_avatar/','api_upload_avatar');

// AUTH
post('/user/register/','api_register_request');
post('/user/login','api_login_user');
