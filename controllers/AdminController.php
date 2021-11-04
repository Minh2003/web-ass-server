<?php

namespace Controllers;

use blog_model;
use dish_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;

class AdminController extends AuthMiddleware
{

  public function checkAdminRole()
  {
    $user_valid = $this->isJWTValid();

    if ($user_valid === FALSE || json_decode($user_valid)->manager === '0') {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  public function createDish()
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $sql = "Insert into dish (name,description,image) Values ('$name','$description','$image')";
    mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $dish = new dish_model($id, $name, $description, $image);
    echo json_encode($dish);
  }

  public function deleteDish($param)
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from dish where id = $id";
    mysqli_query($db, $sql);

    echo json_encode("Successfully!");
  }

  public function createBlog()
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_POST['image'];
    $date = time();

    $sql = "insert into blog(title, content, image) values ('$title','$content', '$image')";
    mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $blog = new blog_model($id, $title, $content, $image, $date);
    echo json_encode($blog);
  }

  public function deleteBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from blog where id = $id";
    mysqli_query($db, $sql);

    echo json_encode("Successfully!");
  }

  public function updateBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_POST['image'];

    $sql = "update blog set title = '$title', content = '$content', image = '$image' where id = '$id'";
    mysqli_query($db, $sql);

    $date = time();
    $blog = new blog_model($id, $title, $content, $image, $date);
    echo json_encode($blog);
  }

  public function deleteUser($param)
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from user where id = $id";
    mysqli_query($db, $sql);

    echo json_encode("Successfully!");

  }

  public function deleteComment()
  {
    if (!$this->checkAdminRole()) {
      echo "Invalid action";
      return;
    }
  }
}
