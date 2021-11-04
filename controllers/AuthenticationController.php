<?php

namespace Controllers;

use user_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;

class AuthenticationController extends AuthMiddleware
{
  public function checkUserExists($username)
  {
    $db = Db::getInstance();
    $sql = "SELECT * FROM user where username = '$username'";

    $result = $db->query($sql);

    return $result;
  }

  public function register()
  {
    $db = Db::getInstance();
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $phoneNumber = $_POST['phoneNumber'];
    $avatar = $_POST['avatar'];
    $manager = 0;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $user = $this->checkUserExists($username);
    if ($user->num_rows > 0) {
      echo "User already exists";
    } else {
      $sql = "insert into user(email, password, username, phoneNumber, manager, avatar) 
            values ('$email', '$hashed_password', '$username', '$phoneNumber', $manager, '$avatar')";
      mysqli_query($db, $sql);
      $id = mysqli_insert_id($db);

      $new_user = new user_model($id, $email, $hashed_password, $username, $phoneNumber, $avatar, $manager);
      $payload = [
        'username' => $username,
        'id' => $id,
        'manager' => $manager,
        'exp' => time() + 60 * 60 * 24 * 30,
      ];
      $token = $this->generateJWT($payload);

      $response = [
        'user' => $new_user,
        'token' => $token
      ];

      echo json_encode($response);
    }
  }

  public function login()
  {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $this->checkUserExists($username);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $user = new user_model($row['id'], $row['email'], $row['password'], $row['username'], $row['phoneNumber'], $row['avatar'], $row['manager']);

      if(password_verify($password, $user->password)) {
        $payload = [
          'username' => $username,
          'id' => $row['id'],
          'manager' => $user->manager,
          'exp' => time() + 60 * 60 * 24 * 30,
        ];
        $token = $this->generateJWT($payload);
  
        $response = [
          'user' => $user,
          'token' => $token
        ];

        echo json_encode($response);
      }
      else {
        echo "Wrong password";
      }
    } else {
      echo "User does not exist";
    }
  }
}
