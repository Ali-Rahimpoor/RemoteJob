<?php
$GLOBALS['router']= [];
function add_route($method,$url,$callback,$regex=[]){
   $GLOBALS['router'][]=[
      'method'=>$method,
      'url'=> $url,
      'regex'=>$regex,
      'callback'=> $callback
   ];
}
function get($url,$callback,$regex=[]){
   add_route('GET',$url,$callback,$regex);
}
function post($url,$callback,$regex=[]){
   add_route('POST',$url,$callback,$regex);
}
function delete($url,$callback,$regex=[]){
   add_route('DELETE',$url,$callback,$regex);
}
function put($url,$callback,$regex=[]){
   add_route('PUT',$url,$callback,$regex);
}
function match_route($methods,$url,$callback,$regex=[]){
     add_route($methods,$url,$callback,$regex);
}
function any_route($url,$callback,$regex=[]){
     add_route('any',$url,$callback,$regex);
}