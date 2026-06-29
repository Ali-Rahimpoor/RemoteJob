<?php

// JOBS
post('/jobs','api_post_job');
put('/jobs','api_put_job');
get('/jobs','api_get_jobs');

// USERS
get('user/profile','api_user_info');
put('user/profile','api_upload_avatar');

// AUTH
post('/user/register','api_register_request');
post('/user/login','api_login_user');
