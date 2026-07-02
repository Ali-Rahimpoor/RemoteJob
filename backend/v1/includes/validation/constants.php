<?php
const USER_ROLES = [   
   'employer',
   'job_seeker'
];
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
   'pending' => 'در انتظار تایید',
   'publish' => 'منتشر شده',
   'expire'  => 'منقضی شده',
   'delete'  => 'حذف شده'
];
CONST JOBS_ORDER = [
   'date' => "created_at"
];
CONST JOBS_SORT = [
   'newest' => "DESC",
   'oldest' => "ASC"
];
CONST JOBS_PER_PAGE = 10;