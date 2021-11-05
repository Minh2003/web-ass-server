<?php

namespace Controllers;

use user_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;

class AuthenticationController extends AuthMiddleware
{
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
      echo json_encode(["message" => "User already exists", 'status' => 401]);
    } else if ((strlen($username) > 50 || strlen($phoneNumber) > 10 || strlen($email) > 50)) {
      echo json_encode(["message" => "Text for username or phone number or email is too long", 'status' => 409]);
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

      $user->password = '';

      $response = [
        'user' => $new_user,
        'token' => $token
      ];

      echo json_encode(["response" => $response, 'status' => 200]);
    }
  }

  public function login()
  {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $this->checkUserExists($username);

    if ($result->num_rows > 0) {
      $row = mysqli_fetch_assoc($result);
      $user = new user_model($row['id'], $row['email'], $row['password'], $row['username'], $row['phoneNumber'], $row['avatar'], $row['manager']);

      if (password_verify($password, $user->password)) {
        $payload = [
          'username' => $username,
          'id' => $row['id'],
          'manager' => $user->manager,
          'exp' => time() + 60 * 60 * 24 * 30,
        ];
        $token = $this->generateJWT($payload);

        $user->password = '';

        $response = [
          'user' => $user,
          'token' => $token
        ];

        echo json_encode(['response' => $response, 'status' => 200]);
      } else {
        echo json_encode(["message" => "Wrong password", 'status' => 401]);
      }
    } else {
      echo json_encode(["message" => "User does not exist", 'status' => 401]);
    }
  }
}
