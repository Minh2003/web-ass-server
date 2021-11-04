<?php
require_once("db.php");
// Định nghĩa hằng Path của file index.php
define('PATH_ROOT', __DIR__);

// Autoload class trong PHP
spl_autoload_register(function (string $class_name) {
    include_once PATH_ROOT . '/' . $class_name . '.php';
});

// load class Route
$router = new Core\Http\Route();


include_once PATH_ROOT . '/routers/admin.php';
include_once PATH_ROOT . '/routers/user.php';
include_once PATH_ROOT . '/routers/authentication.php';

include_once PATH_ROOT . '/models/dish_model.php';
include_once PATH_ROOT . '/models/blog_model.php';
include_once PATH_ROOT . '/models/reservation_model.php';
include_once PATH_ROOT . '/models/user_model.php';
include_once PATH_ROOT . '/models/comment_model.php';

include_once PATH_ROOT . '/middlewares/AuthMiddleware.php';

// Lấy url hiện tại của trang web. Mặc định la /
$request_url = !empty($_GET['url']) ? '/' . $_GET['url'] : '/';

// Lấy phương thức hiện tại của url đang được gọi. (GET | POST). Mặc định là GET.
$method_url = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

// map URL
$router->map($request_url, $method_url);

?>
