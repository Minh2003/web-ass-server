<?php

namespace Middleware;

use Db;

class AuthMiddleware
{
  private $key = 'restaurant';
  private $headersAlg = ['alg' => 'HS256', 'typ' => 'JWT'];

  private function base64url_encode($str)
  {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
  }

  public function generateJWT($payload)
  {
    $headers = $this->headersAlg;
    $header_encoded = $this->base64url_encode(json_encode($headers));
    $payload_encoded = $this->base64url_encode(json_encode($payload));
    $signature = hash_hmac('SHA256', $header_encoded . '.' . $payload_encoded, $this->key, true);
    $signature_encoded = $this->base64url_encode($signature);

    $jwt_token = "$header_encoded.$payload_encoded.$signature_encoded";

    return $jwt_token;
  }

  public function isJWTValid()
  {
    $all_headers = getallheaders();

    if(empty($all_headers) || !array_key_exists('Bear-Token', $all_headers)) {
      return FALSE;
    }
    
    $token = $all_headers['Bear-Token'];
    $token_part = explode('.', $token);
    $header_decode = base64_decode($token_part[0]);
    $payload_decode = base64_decode($token_part[1]);
    $signature_provided_decode = $token_part[2];

    // Check token is expired
    $expiration = json_decode($payload_decode)->exp;
    $is_token_expired = ($expiration - time()) < 0;

    // Check signature
    $base64_url_header = $this->base64url_encode($header_decode);
    $base64_url_payload = $this->base64url_encode($payload_decode);
    $signature_encoded = hash_hmac('SHA256', $base64_url_header . '.' . $base64_url_payload, $this->key, true);
    $base64_url_signature = $this->base64url_encode($signature_encoded);
    $is_signature_valid = ($base64_url_signature == $signature_provided_decode);

    if ($is_token_expired == TRUE || $is_signature_valid == FALSE) {
      return FALSE;
    } else {
      return $payload_decode;
    }
  }

  public function checkUserExists($username)
  {
    $db = Db::getInstance();
    $sql = "SELECT * FROM user where username = '$username'";

    $result = mysqli_query($db, $sql);

    return $result;
  }
}
