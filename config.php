<?php

  global $rkvoters_config;

  // this varies depending on the environment...
  $cwd = getcwd();

  if($cwd == "/var/www/html"){
    $rkvoters_config = array(
      "api_url" => "http://174.138.68.14/api/app.php"
    );
  }
  else {
    $rkvoters_config = array(
      "api_url" => "http://localhost/biz/_rkvoter/data-api/api/app.php"
    );
  }
