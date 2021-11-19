<?php
   $router->get('/blogs', 'UserController@getBlogAll');
   $router->get('/blog/{id}', 'UserController@getBlogDetail');
   $router->get('/dish/{id}', 'UserController@getDishDetail');
   $router->get('/menu', 'UserController@getMenu');

   $router->post('/reservation', 'UserController@reservation');
   
   $router->post('/comment/create', 'UserController@createComment');
   $router->post('/comment/delete/{id}', 'UserController@deleteComment');

   $router->post('/auth/update_profile', 'UserController@updateProfile');
   $router->post('/auth/update_password', 'UserController@updatePassword');
?>