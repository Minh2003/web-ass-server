<?php
$router->post('/admin/create_dish', 'AdminController@createDish');
$router->post('/admin/delete_dish/{id}', 'AdminController@deleteDish');

$router->post('/admin/create_blog', 'AdminController@createBlog');
$router->post('/admin/delete_blog/{id}', 'AdminController@deleteBlog');
$router->post('/admin/update_blog/{id}', 'AdminController@updateBlog');
?>