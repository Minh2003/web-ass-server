<?php

namespace Controllers;

use user_model;
use Db;
use Middleware\AuthMiddleware as AuthMiddleware;
use Middleware\FormMiddleware as FormMiddleware;

class AuthenticationController
{
  public function register()
  {
    $payload = ['email', 'password', 'username', 'phoneNumber', 'avatar', 'verify_password'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);

    if ($check) {
      $email = $_POST['email'];
      $password = $_POST['password'];
      $verify_password = $_POST['verify_password'];
      $username = $_POST['username'];
      $phoneNumber = $_POST['phoneNumber'];
      $avatar = $_POST['avatar'];
      $manager = 0;
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      if ($password != $verify_password) {
        echo json_encode(['message' => 'Passwords do not match', 'status' => 400]);
        return;
      }

      $check = $formValid->lengthValidator(10, 10, $phoneNumber)
        && $formValid->emailValidator($email)
        && $formValid->lengthValidator(0, 51, $username);
      if (!$check) {
        echo json_encode(['message' => "Invalid data", 'status' => 409]);
      } else {
        $db = Db::getInstance();

        $auth = new AuthMiddleware();
        $user = $auth->checkUserExists($username);
        if ($user->num_rows > 0) {
          echo json_encode(["message" => "User already exists", 'status' => 401]);
        } else {
          $sql = "insert into user(email, password, username, phoneNumber, manager, avatar) 
                values ('$email', '$hashed_password', '$username', '$phoneNumber', $manager, '$avatar')";
          mysqli_query($db, $sql);
          $id = mysqli_insert_id($db);

          $new_user = new user_model($id, $email, '', $username, $phoneNumber, $avatar, $manager);
          if ($id) {
            $payload = [
              'username' => $username,
              'id' => $id,
              'manager' => $manager,
              'exp' => time() + 60 * 60 * 24 * 30,
            ];
            $token = $auth->generateJWT($payload);

            $user->password = '';

            $response = [
              'user' => $new_user,
              'token' => $token
            ];

            echo json_encode(["response" => $response, 'status' => 200]);
          } else {
            echo json_encode(['message' => "Server or database is error", 'status' => 500]);
          }
        }
      }
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }

  public function login()
  {
    $payload = ['username', 'password'];
    $formValid = new FormMiddleware();
    $check = $formValid->checkFullFields($payload);
    if ($check) {
      $username = $_POST['username'];
      $password = $_POST['password'];

      $auth = new AuthMiddleware();
      $result = $auth->checkUserExists($username);

      if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
          $user = new user_model($row['id'], $row['email'], $row['password'], $row['username'], $row['phoneNumber'], $row['avatar'], $row['manager']);
          $payload = [
            'username' => $username,
            'id' => $row['id'],
            'manager' => $user->manager,
            'exp' => time() + 60 * 60 * 24 * 30,
          ];
          $token = $auth->generateJWT($payload);

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
    } else {
      echo json_encode(['message' => "Missing some fields", 'status' => 400]);
    }
  }
}
