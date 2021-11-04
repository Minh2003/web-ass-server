<?php
   $router->get('/blogs', 'UserController@getBlogAll');
   $router->get('/blog/{id}', 'UserController@getBlogDetail');
   $router->get('/menu', 'UserController@getMenu');

   $router->post('/reservation', 'UserController@reservation');
?>