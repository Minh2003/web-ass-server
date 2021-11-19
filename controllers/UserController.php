<?php

namespace Controllers;

use blog_model;
use dish_model;
use comment_model;
use user_model;
use reservation_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;
use Middleware\FormMiddleware as FormMiddleware;
use mysqli;

class UserController
{
  public function updatePassword()
  {
    $authMiddleware = new AuthMiddleware();
    $user_valid = $authMiddleware->isJWTValid();
    if (!$user_valid) {
      echo json_encode(["message" => "Token is expired, please login again.", 'status' => 408]);
      return;
    }

    $db = Db::getInstance();
    $id = json_decode($user_valid)->id;

    $payload = ['old_password', 'new_password', 'verify_password'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $old_password = $_POST['old_password'];
      $new_password = $_POST['new_password'];
      $verify_password = $_POST['verify_password'];

      $check = $formValid->lengthValidator(6, 100, $new_password)
        && $formValid->lengthValidator(6, 100, $old_password)
        && $formValid->lengthValidator(6, 100, $verify_password);

      if ($check) {
        $sql = "SELECT password from user where id = $id";
        $row = mysqli_query($db, $sql);
        $password = mysqli_fetch_array($row)['password'];

        if (password_verify($old_password, $password) && $new_password == $verify_password) {
          $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
          $sql = "update user set password = '$hashed_password' where id = $id";
          $row = mysqli_query($db, $sql);

          if ($row == TRUE) {
            echo json_encode(['message' => "Update password successfully. Please login again.", 'status' => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        } else {
          echo json_encode(['message' => "Invalid action[verify != new || old_password is wrong]", 'status' => 401]);
        }
      } else {
        echo json_encode(['message' => "Invalid some fields", 'status' => 400]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function updateProfile()
  {
    $authMiddleware = new AuthMiddleware();
    $user_valid = $authMiddleware->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "Token is expired, please login again.", 'status' => 408]);
      return;
    }

    $id = json_decode($user_valid)->id;
    $manager = json_decode($user_valid)->manager;

    $payload = ['username', 'email', 'avatar', 'phoneNumber'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check == TRUE) {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $avatar = $_POST['avatar'];
      $phoneNumber = $_POST['phoneNumber'];

      $check = $formValid->emailValidator($email)
        && $formValid->lengthValidator(10, 10, $phoneNumber)
        && $formValid->lengthValidator(1, 50, $username);

      if ($check) {
        $db = Db::getInstance();
        $sql = "select username from user where id = $id";
        $row = mysqli_query($db, $sql);

        $old_username = mysqli_fetch_assoc($row)['username'];
        $user = $authMiddleware->checkUserExists($username);

        if ($old_username == $username) {
          $sql = "update user set username = '$username', email = '$email', avatar = '$avatar', phoneNumber = '$phoneNumber' where id = '$id'";

          $result = mysqli_query($db, $sql);

          if ($result == TRUE) {
            $new_user = new user_model($id, $email, '', $username, $phoneNumber, $avatar, $manager);

            echo json_encode(['response' => $new_user, 'status' => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        } else {
          if ($user->num_rows > 0) {
            echo json_encode(['message' => "Username is already exists"]);
            return;
          }
          $sql = "update user set username = '$username', email = '$email', avatar = '$avatar', phoneNumber = '$phoneNumber' where id = '$id'";

          $result = mysqli_query($db, $sql);

          if ($result == TRUE) {
            $new_user = new user_model($id, $email, '', $username, $phoneNumber, $avatar, $manager);

            echo json_encode(['response' => $new_user, 'status' => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        }
      } else {
        echo json_encode(['message' => "Invalid some fields", 'status' => 400]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function createComment()
  {
    $authMiddleware = new AuthMiddleware();
    $user_valid = $authMiddleware->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "You are not allowed to comment on this blog", 'status' => 405]);
      return;
    }

    $db = Db::getInstance();
    $payload = ['blogId', 'description'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $blogId = $_POST['blogId'];
      $description = $_POST['description'];


      $sql = "select * from blog where id = $blogId";
      $row = mysqli_query($db, $sql);

      if ($row->num_rows > 0) {
        $check = $formValid->lengthValidator(0, 1000, $description);

        if ($check) {
          $userId = json_decode($user_valid)->id;

          $sql = "select * from user where id = $userId";
          $row = mysqli_query($db, $sql);
          if ($row->num_rows == 0) {
            echo json_encode(['message' => "You are not allowed to comment on this blog", 'status' => 405]);
            return;
          }

          $sql = "insert into comment (blogId, userId, description) values('$blogId', '$userId', '$description')";
          mysqli_query($db, $sql);
          $id = mysqli_insert_id($db);

          if ($id) {
            $comment = new comment_model($id, $userId, $blogId, $description);

            echo json_encode(['response' => $comment, 'status' => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        } else {
          echo json_encode(['message' => "Comment is too long", 'status' => 400]);
        }
      } else {
        echo json_encode(["message" => "Blog $blogId is not exist.", 'status' => 409]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function deleteComment($param)
  {
    $authMiddleware = new AuthMiddleware();
    $user_valid = $authMiddleware->isJWTValid();
    if (!$user_valid) {
      echo json_encode(['message' => "Token is expired or You are not allowed to delete this comment", 'status' => 408]);
      return;
    }

    $user_id = json_decode($user_valid)->id;

    $payload = ['blogId'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {

      $db = Db::getInstance();
      $sql = "select * from user where id = $user_id";
      $row = mysqli_query($db, $sql);
      if ($row->num_rows == 0) {
        echo json_encode(['message' => "You are not allowed to comment on this blog", 'status' => 405]);
        return;
      }

      $blog_id = $_POST['blogId'];
      $comment_id = substr($param, 1, -1);

      $sql = "SELECT * from comment where id = $comment_id";
      $row = mysqli_query($db, $sql);

      if ($row->num_rows > 0) {
        $result = mysqli_fetch_assoc($row);
        if ($result['userId'] != $user_id) {
          echo json_encode(["message" => "You do not have permission to delete this comment", "status" => 401]);
        } else if ($result['blogId'] != $blog_id) {
          echo json_encode(["message" => "Comment $comment_id does not exist in blog $blog_id", "status" => 409]);
        } else {
          $sql = "DELETE FROM comment WHERE id = $comment_id";
          $row = mysqli_query($db, $sql);

          if ($row) {
            echo json_encode(["response" => "Delete comment successfully", "status" => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        }
      } else {
        echo json_encode(["message" => "Comment $comment_id not found", "status" => 409]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function getBlogAll()
  {
    $list = [];

    $db = Db::getInstance();
    $sql = 'SELECT * FROM blog';
    $result = mysqli_query($db, $sql);

    if ($result->num_rows > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $list[] = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);
      }

      echo json_encode(['response' => $list, 'status' => 200]);
    } else {
      echo json_encode(['message' => "Server or database is error", 'status' => 500]);
    }
  }

  public function getBlogDetail($param)
  {

    $id = substr($param, 1, -1);
    $list = [];

    $db = Db::getInstance();
    $sql = "SELECT * FROM blog where id = $id";
    $result = mysqli_query($db, $sql);
    if ($result->num_rows) {
      $row = mysqli_fetch_assoc($result);
      $blog = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);

      $sql = "SELECT * FROM comment where  blogId = $id";
      $result = mysqli_query($db, $sql);
      while ($row = mysqli_fetch_assoc($result)) {
        $a_comment = new comment_model($row['id'], $row['userId'], $row['blogId'], $row['description']);
        $sql_i = "select username, avatar from user where id =" . $row['userId'];
        $result_i = mysqli_query($db, $sql_i);
        $user_info = mysqli_fetch_assoc($result_i);
        $new_element = (object) array_merge((array)$a_comment, (array)$user_info);

        $list[] = $new_element;
      }
      $response = [
        'blog' => $blog,
        'comments' => $list,
      ];

      echo json_encode(['response' => $response, 'status' => 200]);
    } else {
      echo json_encode(['message' => "Server or database is error", 'status' => 500]);
    }
  }

  public function getDishDetail($param)
  {
    $id = substr($param, 1, -1);

    $db = Db::getInstance();
    $sql = "SELECT * FROM dish where id = $id";
    $result = mysqli_query($db, $sql);

    if ($result->num_rows) {
      $row = mysqli_fetch_assoc($result);
      $dish = new dish_model($row['id'], $row['name'], $row['description'], $row['image']);

      echo json_encode(['response' => $dish, 'status' => 200]);
    } else {
      echo json_encode(['message' => "Server or database is error", 'status' => 500]);
    }
  }

  public function getMenu()
  {
    $list = [];

    $db = Db::getInstance();
    $sql = 'SELECT * FROM dish';
    $result = mysqli_query($db, $sql);

    if ($result->num_rows) {
      while ($row = mysqli_fetch_assoc($result)) {
        $list[] = new dish_model($row['id'], $row['name'], $row['description'], $row['image']);
      }

      echo json_encode(['response' => $list, 'status' => 200]);
    } else {
      echo json_encode(['message' => "Server or database is error", 'status' => 500]);
    }
  }

  public function reservation()
  {
    $db = Db::getInstance();

    $payload = ['description', "NoP", "name", "email", "phoneNumber", "date", "time"];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $phoneNumber = $_POST['phoneNumber'];
      $time = $_POST['time'];
      $description = $_POST['description'];
      $NoP = $_POST['NoP'];
      $date = $_POST['date'];

      if ($NoP < 1 || $NoP > 30) {
        echo json_encode(['message' => "Invalid amount person (person must be between 1 and 30)", 'status' => 409]);
        return;
      }

      $check = $formValid->emailValidator($email)
        && $formValid->lengthValidator(10, 10, $phoneNumber)
        && $formValid->lengthValidator(1, 30, $name)
        && $formValid->dateValidator($date)
        && $formValid->timeValidator($time);
      if ($check) {
        $sql = "insert into reservation (name, email, phoneNumber, NoP, date, time, description) values('$name', '$email', '$phoneNumber', $NoP, '$date', '$time', '$description')";
        $result = mysqli_query($db, $sql);
        $id = mysqli_insert_id($db);

        if ($result) {
          $new_reservation = new reservation_model($id, $name, $email, $phoneNumber, $NoP, $date, $time, $description);

          echo json_encode(['response' => $new_reservation, 'status' => 200]);
        } else {
          echo json_encode(['message' => "Server or database is error", 'status' => 500]);
        }
      } else {
        echo json_encode(['message' => "Invalid data", 'status' => 409]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }
}
