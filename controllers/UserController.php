<?php

namespace Controllers;

use blog_model;
use dish_model;
use comment_model;
use reservation_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;

class UserController extends AuthMiddleware
{
  public function updateProfile()
  {
  }

  public function createComment()
  {
    $user_valid = $this->isJWTValid();
    if(!$user_valid) {
      echo "You are not allowed to comment on this blog";
      return;
    }

    $db = Db::getInstance();

    $blogId = $_POST['blogId'];
    $description = $_POST['description'];
    $userId = json_decode($user_valid)->id;

    $sql = "insert into comment (blogId, userId, description) values('$blogId', '$userId', '$description')";
    mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $comment = new comment_model($id, $userId, $blogId, $description);    

    echo json_encode($comment);
  }

  public function deleteComment()
  {
  }

  public function getBlogAll()
  {
    $db = Db::getInstance();

    $list = [];
    $sql = 'SELECT * FROM blog';
    $result = mysqli_query($db, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);
    }

    echo json_encode($list);
  }

  public function getBlogDetail($param)
  {
    $db = Db::getInstance();

    $id = substr($param, 1, -1);
    $list = [];

    $sql = "SELECT * FROM blog where id = $id";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($result);
    $blog = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);
    
    $sql = "SELECT * FROM comment where  blogId = $id";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new comment_model($row['id'], $row['blogId'], $row['userId'], $row['description']);
    }

    $response = [
      'blog' => $blog,
      'comments' => $list,
    ];

    echo json_encode($response);
  }

  public function getMenu()
  {
    $db = Db::getInstance();

    $list = [];
    $sql = 'SELECT * FROM dish';
    $result = mysqli_query($db, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new dish_model($row['id'], $row['name'], $row['description'], $row['image']);
    }

    echo json_encode($list);
  }

  public function reservation()
  {
    $db = Db::getInstance();

    $date = time();
    $description = $_POST['description'];
    $NoP = $_POST['NoP'];

    $sql = "insert into reservation (description, NoP) values ('$description', '$NoP')";
    $result = mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $result = new reservation_model($id, $date, $description, $NoP);
    echo json_encode($result);
  }
}
