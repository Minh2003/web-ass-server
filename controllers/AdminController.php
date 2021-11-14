<?php

namespace Controllers;

use blog_model;
use dish_model;
use comment_model;
use user_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;
use Middleware\FormMiddleware as FormMiddleware;

class AdminController
{
  public function checkAdminRole()
  {
    $authMiddleware = new AuthMiddleware();
    $user_valid = $authMiddleware->isJWTValid();

    if ($user_valid == FALSE || json_decode($user_valid)->manager == 0) {
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

    $payload = ['name', 'description', 'image'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $name = $_POST['name'];
      $description = $_POST['description'];
      $image = $_POST['image'];

      $db = Db::getInstance();
      $sql = "Insert into dish (name,description,image) Values ('$name','$description','$image')";
      $row = mysqli_query($db, $sql);
      $id = mysqli_insert_id($db);

      if ($row) {
        $dish = new dish_model($id, $name, $description, $image);
        echo json_encode(["response" => $dish, 'status' => 200]);
      } else {
        echo json_encode(["message" => "Server of database is error", 'status' => 500]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function updateDish($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $payload = ['name', 'description', 'image'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $id = substr($param, 1, -1);

      $db = Db::getInstance();
      $sql = "select * from dish where id = $id";
      $row = mysqli_query($db, $sql);

      if ($row->num_rows > 0) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $image = $_POST['image'];

        $sql = "Update dish set name = '$name', description = '$description', image = '$image' where id = $id";
        $row = mysqli_query($db, $sql);

        if ($row) {
          $dish = new dish_model($id, $name, $description, $image);
          echo json_encode(["response" => $dish, 'status' => 200]);
        } else {
          echo json_encode(["message" => "Server of database is error", 'status' => 500]);
        }
      } else {
        echo json_encode(["message" => "Dish $id not found", 'status' => 404]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function deleteDish($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $id = substr($param, 1, -1);

    $db = Db::getInstance();
    $sql = "SELECT * FROM dish WHERE id =$id";
    $row = mysqli_query($db, $sql);
    if ($row->num_rows == 0) {
      echo json_encode(["message" => "Dish not found", 'status' => 404]);
      return;
    }

    $sql = "delete from dish where id = $id";
    $row = mysqli_query($db, $sql);

    if ($row) {
      echo json_encode(["response" => "Successfully!", 'status' => 200]);
    } else {
      echo json_encode(["message" => "Server of database is error", 'status' => 500]);
    }
  }

  public function createBlog()
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $payload = ['title', 'content', 'image'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $title = $_POST['title'];
      $content = $_POST['content'];
      $image = $_POST['image'];
      $date = time();

      $db = Db::getInstance();
      $sql = "insert into blog(title, content, image) values ('$title','$content', '$image')";
      $row = mysqli_query($db, $sql);
      $id = mysqli_insert_id($db);
      if ($row) {
        $blog = new blog_model($id, $title, $content, $image, $date);
        echo json_encode(['response' => $blog, 'status' => 200]);
      } else {
        echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function deleteBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $id = substr($param, 1, -1);

    $db = Db::getInstance();
    $sql = "SELECT * FROM blog WHERE id =$id";
    $row = mysqli_query($db, $sql);
    if ($row->num_rows == 0) {
      echo json_encode(["message" => "Blog $id not found", 'status' => 404]);
      return;
    }

    $sql = "delete from comment where blogId = $id";
    $row = mysqli_query($db, $sql);

    if ($row) {
      $sql = "delete from blog where id = $id";
      $row = mysqli_query($db, $sql);
      if ($row) {
        echo json_encode(["response" => "Successfully!", 'status' => 200]);
      } else {
        echo json_encode(["message" => "Server of database is error", 'status' => 500]);
      }
    } else {
      echo json_encode(["message" => "Server of database is error", 'status' => 500]);
    }
  }

  public function updateBlog($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $payload = ['title', 'content', 'image'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $id = substr($param, 1, -1);

      $db = Db::getInstance();
      $sql = "select * from blog where id = $id";
      $row = mysqli_query($db, $sql);
      if ($row->num_rows > 0) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $image = $_POST['image'];
        $date = time();

        $sql = "update blog set title = '$title', content = '$content', image = '$image' where id = $id";
        $row = mysqli_query($db, $sql);
        if ($row) {
          $sql = "SELECT * from comment where blogId = $id";
          $result = mysqli_query($db, $sql);
          if ($result) {
            $list = [];
            while ($row = mysqli_fetch_assoc($result)) {
              $list[] = new comment_model($row['id'], $row['userId'], $row['blogId'], $row['description']);
            }

            $blog = new blog_model($id, $title, $content, $image, $date);
            echo json_encode(['response' => ["blog" => $blog, "comments" => $list], 'status' => 200]);
          } else {
            echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
          }
        } else {
          echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
        }
      } else {
        echo json_encode(['message' => "Blog $id not found", 'status' => 404]);
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function getAllUsers()
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $db = Db::getInstance();
    $sql = "SELECT * FROM user";
    $list = [];

    $result = mysqli_query($db, $sql);
    if ($result && $result->num_rows) {
      while ($row = mysqli_fetch_assoc($result)) {
        $list[] = new user_model($row['id'], $row['email'], '', $row['username'], $row['phoneNumber'], $row['avatar'], $row['manager']);
      }
      echo json_encode(["response" => $list, 'status' => 200]);
    } else {
      echo json_encode(["message" => "No user found || Server of database is error", 'status' => 500]);
    }
  }

  public function deleteUser($param)
  {
    $user_valid = $this->checkAdminRole();
    if (!$user_valid) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $id = substr($param, 1, -1);
    $admin_id = json_decode($user_valid)->id;

    if ($id == $admin_id) {
      echo json_encode(["message" => "You can't delete yourself!", "status" => 403]);
      return;
    }

    $db = Db::getInstance();
    $sql = "select * from user where id = $id";
    $row = mysqli_query($db, $sql);
    if ($row->num_rows > 0) {
      // Delete all comment
      $sql = "delete from comment where userId = $id";
      $row = mysqli_query($db, $sql);
      if ($row == TRUE) {
        // Delete user
        $sql = "delete from user where id = $id";
        $row = mysqli_query($db, $sql);

        if ($row == TRUE) {
          echo json_encode(["response" => "Successfully!", "status" => 200]);
        } else {
          echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
        }
      } else {
        echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
      }
    } else {
      echo json_encode(["message" => "User $id not found", 'status' => 404]);
    }
  }

  public function deleteComment($param)
  {
    if (!$this->checkAdminRole()) {
      echo json_encode(["message" => "Invalid action. You are not admin", 'status' => 403]);
      return;
    }

    $comment_id = substr($param, 1, -1);

    $db = Db::getInstance();
    $sql = "select * from comment where id = $comment_id";
    $row = mysqli_query($db, $sql);
    if ($row->num_rows > 0) {
      $sql = "delete from comment where id = $comment_id";
      $row = mysqli_query($db, $sql);
      if ($row == TRUE) {
        echo json_encode(["response" => "Successfully!", "status" => 200]);
      } else {
        echo json_encode(['message' => 'Server of database is error', 'status' => 500]);
      }
    } else {
      echo json_encode(["message" => "Comment $comment_id not found", 'status' => 404]);
    }
  }
}
