<?php

$router->post('authorize','TCXController@authorize')->middleware('api');
$router->post('reauthorize','TCXController@reauthorize')->middleware('api');