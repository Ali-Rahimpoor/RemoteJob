<?php
mysqli_report(MYSQLI_REPORT_ERROR);
$db = @mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if(!$db){
   $db_error = mysqli_connect_error();
   
   // دریافت اطلاعات دقیق خطا
   $backtrace = debug_backtrace();
   $caller = $backtrace[0];
   $line_number = $caller['line'];
   $file_name = $caller['file'];
   
   // اطلاعات کامل لاگ
   $log_message = date('Y-m-d H:i:s') . " | " .
                  "Error: " . $db_error . " | " .
                  "File: " . $file_name . " | " .
                  "Line: " . $line_number . " | " .
                  "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . PHP_EOL .
                  "URL: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . PHP_EOL .
                  "----------------------------------------" . PHP_EOL;
   
   file_put_contents('db-error.txt', $log_message, FILE_APPEND);
   include "db-error.php";
   exit;
}