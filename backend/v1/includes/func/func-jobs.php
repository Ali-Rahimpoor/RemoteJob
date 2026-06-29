<?php
CONST JOBS_DURATIONS =[
   '1_week'   => '۱ هفته',
   '2_weeks'  => '۲ هفته',
   '3_weeks'  => '۳ هفته',
   '1_month'  => '۱ ماه',
   '2_months' => '۲ ماه',
   '3_months' => '۳ ماه',
   '6_months' => '۶ ماه',
   '1_year'   => '۱ سال',
   'more_than_year' => 'بیش از ۱ سال',
   'flexible' => 'قابل توافق',
];
CONST JOBS_SALARAIES = [
   'below_5'    => 'کمتر از ۵ میلیون',
   '5_10'       => '۵ تا ۱۰ میلیون',
   '10_20'      => '۱۰ تا ۲۰ میلیون',
   '20_30'      => '۲۰ تا ۳۰ میلیون',
   '30_50'      => '۳۰ تا ۵۰ میلیون',
   '50_100'     => '۵۰ تا ۱۰۰ میلیون',
   'above_100'  => 'بیش از ۱۰۰ میلیون',
   'negotiable' => 'قابل توافق',
];
CONST JOBS_STATUSES = [
   'oending' => 'در انتظار تایید',
   'publish' => 'منتشر شده',
   'expire'  => 'منقضی شده',
   'delete'  => 'حذف شده'
];
function valid_salary($salary){
   if(array_key_exists($salary,JOBS_SALARAIES)){
      return $salary;
   }
      return false;
}
function valid_duration($duratoin){
   if(array_key_exists($duratoin,JOBS_DURATIONS)){
      return $duratoin;
   }
      return  false;
}
function valid_status($status){
   if(array_key_exists($status,JOBS_STATUSES)){
      return $status;
   }
   return false;
}