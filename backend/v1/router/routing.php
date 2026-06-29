<?php
$uri = $_SERVER['REQUEST_URI'];
// die($uri);
$uri = substr($uri,27);
$parsed = parse_url($uri);
$uri = $parsed['path'];
// die($uri);

$request_method = $_SERVER['REQUEST_METHOD'];

foreach($GLOBALS['router'] as $route){
   
   $url = $route['url'];
   $methods = (array) $route['method'];
   $callback = $route['callback'];
   $regex = isset($route['regex']) ? $route['regex'] : [];
   
   
   $url = preg_replace_callback('/\{([a-z0-9_\?]+)\}/i',function($matches) use($regex){
      $key = $matches[1];
      $optinal = str_ends_with($key,'?') ? '?' : '';
      
      if( isset($regex[$key])){
         return '(' . $regex[$key] . ')' . $optinal;
      }
      return '([a-z0-9-]+)' . $optinal;
   },$url);
   $url = trim($url,'/');  // -> musics/([a-z0-9\-]+)
   $url = str_replace('/','\/',$url); // -> musics\/([a-z0-9\-]+)
   $pattern = '/^' . $url . '\/?$/i'; // -> /^musics\/([a-z0-9\-]+)\/?/i
   // die($pattern);
   if(
    (  in_array($request_method,$methods) || $methods[0] == "any" )
      && 
      preg_match($pattern,$uri,$matches)
     ){
      $args = $matches;
      unset($args[0]);
      call_user_func_array($callback,$args);
      break;
   }
}
die("not found route");