<?php

namespace Controllers;

use blog_model;
use dish_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;
use user_model;

class AdminController extends AuthMiddleware
{

  public function checkAdminRole()
  {
    $user_valid = $this->isJWTValid();

    if ($user_valid === FALSE || json_decode($user_valid)->manager === '0') {
      return FALSE;
    } else {
      return $user_valid;
    }
  }

  public function createDish()
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
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
    echo json_encode(["response" => $dish, 'status' => 200]);
  }

  public function deleteDish($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from dish where id = $id";
    mysqli_query($db, $sql);

    echo json_encode(["result" => "Successfully!", 'status' => 200]);
  }

  public function createBlog()
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
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
    echo json_encode(['response' => $blog, 'status' => 200]);
  }

  public function deleteBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from blog where id = $id";
    mysqli_query($db, $sql);

    echo json_encode(['response' => "Successfully!", 'status' => 200]);
  }

  public function updateBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
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
    echo json_encode(['response' => $blog, 'status' => 200]);
  }

  public function getAllUsers() {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $db = Db::getInstance();
    $sql = "SELECT * FROM user";
    $list = [];

    $result = mysqli_query($db, $sql);
    if($result->num_rows) {
      while($row = mysqli_fetch_assoc($result)) {
        $list[] = new user_model($row['id'], $row['email'], '', $row['username'], $row['phoneNumber'], $row['avatar'], $row['manager']);
      }
      
      echo json_encode(["response" => $list, 'status' => 200]);
    }
    else {
      echo json_encode(["message" => "No user found", 'status' => 404]);
    }
  }

  public function deleteUser($param)
  {
    $user_valid = $this->checkAdminRole();
    if (!$user_valid) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }
    $db = Db::getInstance();
    
    $id = substr($param, 1, -1);
    $admin_id = json_decode($user_valid)->id;
    
    if($id === $admin_id) {
      echo json_encode(["message"=>"You can't delete yourself!", "status" => 403]);
      return;
    }

    // Delete all comment
    $sql = "delete from comment where userId = $id";
    $row = mysqli_query($db, $sql);
    if($row === TRUE) {
      // Delete user
      $sql = "delete from user where id = $id";
      mysqli_query($db, $sql);
  
      echo json_encode(['response'=>"Successfully!", 'status'=> 200]);
    }
    else {
      echo json_encode(["message" => "Server Error", 'status' => 500]);
    }
  }

  public function deleteComment($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $db = Db::getInstance();
    $comment_id = substr($param, 1, -1);

    $sql = "DELETE FROM comment WHERE id = $comment_id";
    $row = mysqli_query($db, $sql);

    if($row === TRUE) {
      echo json_encode(["response" => "Comment deleted successfully by admin", 'status' => 200]);
    }
    else {
      echo json_encode(["message" => "Server Error", 'status' => 500]);
    }
  }
}
