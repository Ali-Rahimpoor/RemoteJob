<?php

// JOBS
post('/jobs','api_post_job');
put('/jobs','api_put_job');
get('/jobs','api_get_jobs');
get('/jobs/{slug}','api_get_job',['slug'=>'\d+']);

// PROPOSAL
post('/jobs/{slug}/proposals', 'api_post_proposal', ['slug' => '\d+']);

// SKILLS
post('/skills','api_post_skill');
get("/skills","api_get_skills");
// USERS
get('user/profile','api_user_info');
post('user/profile/avatar','api_upload_avatar');

// AUTH
post('/user/register','api_register_request');
post('/user/login','api_login_user');

