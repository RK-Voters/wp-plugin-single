<?php

  global $rkvoters_config;

  // this varies depending on the environment...
  $cwd = getcwd();

  if($cwd == "/var/www/html"){
    $rkvoters_config = array(
      "api_url" => "http://159.89.38.12/api/"
    );
  }
  else {
    $rkvoters_config = array(
      "api_url" => "http://localhost/dion_data/api/"
    );
  }
