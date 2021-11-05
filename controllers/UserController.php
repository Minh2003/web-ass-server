<?php

namespace Controllers;

use blog_model;
use dish_model;
use comment_model;
use user_model;
use reservation_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;

class UserController extends AuthMiddleware
{
  public function updatePassword()
  {
    $user_valid = $this->isJWTValid();
    if (!$user_valid) {
      echo json_encode(["message" => "Token is expired, please login again.", 'status' => 408]);
      return;
    }

    $db = Db::getInstance();
    $id = json_decode($user_valid)->id;
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $verify_password = $_POST['verify_password'];

    $sql = "SELECT password from user where id = $id";
    $row = mysqli_query($db, $sql);
    $password = mysqli_fetch_array($row)['password'];

    if (password_verify($old_password, $password) && $new_password === $verify_password) {
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $sql = "update user set password = '$hashed_password' where id = $id";
      $row = mysqli_query($db, $sql);

      if ($row === TRUE) {
        echo json_encode(['message' => "Update password successfully. Please login again.", 'status' => 200]);
      }
    } else {
      echo json_encode(['message' => "Invalid action[verify != new || old_password is wrong]", 'status' => 401]);
    }
  }

  public function updateProfile()
  {
    $user_valid = $this->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "Token is expired, please login again.", 'status' => 408]);
      return;
    }

    $id = json_decode($user_valid)->id;

    $manager = json_decode($user_valid)->manager;
    $username = $_POST['username'];
    $email = $_POST['email'];
    $avatar = $_POST['avatar'];
    $phoneNumber = $_POST['phoneNumber'];

    $user = $this->checkUserExists($username);
    if ($user->num_rows > 0) {
      echo json_encode(['message' => "Username is already exists"]);
      return;
    }

    if (strlen($username) > 50 || strlen($phoneNumber) > 10 || strlen($email) > 50) {
      echo json_encode(['message' => "Text for username or phone number or email is too long", 'status' => 409]);
      return;
    }

    $db = Db::getInstance();
    $sql = "update user set username = '$username', email = '$email', avatar = '$avatar', phoneNumber = '$phoneNumber' where id = '$id'";

    $result = mysqli_query($db, $sql);

    if ($result === TRUE) {
      $new_user = new user_model($id, $email, '', $username, $phoneNumber, $avatar, $manager);

      echo json_encode(['response' => $new_user, 'status' => 200]);
    }
  }

  public function createComment()
  {
    $user_valid = $this->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "You are not allowed to comment on this blog", 'status' => 405]);
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

    echo json_encode(['response' => $comment, 'status' => 200]);
  }

  public function deleteComment($param)
  {
    $user_valid = $this->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "Token is expired or You are not allowed to delete this comment", 'status' => 408]);
      return;
    }

    $db = Db::getInstance();
    $comment_id = substr($param, 1, -1);
    $user_id = json_decode($user_valid)->id;

    // Check is user allowed to delete this comment
    $sql = "select * from comment where id = $comment_id";
    $row = mysqli_query($db, $sql);

    if ($row->num_rows > 0) {
      if (mysqli_fetch_assoc($row)['userId'] !== $user_id) {
        echo json_encode(['message' => "You are not allowed to delete this comment", 'status' => 405]);
      } else {
        $sql = "DELETE FROM comment where id = $comment_id";
        $row = mysqli_query($db, $sql);

        if ($row === TRUE) {
          echo json_encode(['message' => "Comment deleted successfully", 'status' => 200]);
        }
      }
    } else {
      echo json_encode(["message" => "Comment is not exist. Please check your comment id", 'status' => 400]);
    }
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

    echo json_encode(['response' => $list, 'status' => 200]);
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

    echo json_encode(['response' => $response, 'status' => 200]);
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

    echo json_encode(['response' => $list, 'status' => 200]);
  }

  public function reservation()
  {
    $db = Db::getInstance();

    $date = time();
    $description = $_POST['description'];
    $NoP = $_POST['NoP'];

    if ($NoP < 1 || $NoP > 30) {
      echo json_encode(['message' => "Invalid amount person (person must be between 1 and 30)", 'status' => 409]);
      return;
    }

    $sql = "insert into reservation (description, NoP) values ('$description', '$NoP')";
    $result = mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $result = new reservation_model($id, $date, $description, $NoP);
    echo json_encode(['response' => $result, 'status' => 200]);
  }
}
