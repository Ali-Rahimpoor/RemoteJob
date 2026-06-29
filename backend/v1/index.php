<?php
require 'init.php';

setup_api_user();

require (ABSPATH . 'api-callbacks-loader.php');

require  (ABSPATH . "router/router.php");

require (ABSPATH . "router/routing.php" );

send_json(
   [  'success'=> false,
      'message'=> 'Route not Found !'
   ],404
);