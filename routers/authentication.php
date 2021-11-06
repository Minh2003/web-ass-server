<?php
  $router->post('/auth/login', 'AuthenticationController@login');
  $router->post('/auth/register', 'AuthenticationController@register');
?>