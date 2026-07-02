<?php
function get_job_from_slug($slug){
    $sql = "SELECT * FROM jobs WHERE slug = '$slug' LIMIT 1";
    $res = db_query($sql);
    if($res && $res -> num_rows){
        return mysqli_fetch_assoc($res);
    }
    return false;
}
function user_has_proposal($job_id,$user_id){
    $sql = "SELECT ID from job_applications WHERE job_id = '$job_id' AND user_id = '$user_id' LIMIT 1 ";
    $res = db_fetch_column($sql);
    if($res){
        return true;
    }
    return false;
}