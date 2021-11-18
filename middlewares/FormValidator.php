<?php

namespace Middleware;

class FormMiddleware
{
  public function checkFullFields($payload)
  {
    for ($i = 0; $i < count($payload); $i++) {
      if (!isset($_POST[$payload[$i]])) {
        return FALSE;
      }
    }
    return TRUE;
  }

  public function emailValidator($email)
  {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function lengthValidator($minLength, $maxLength, $name) {
    return strlen($name) <= $maxLength && strlen($name) >= $minLength;
  }

  public function dateValidator($date) {
    $date = explode('-', $date);
    if (count($date) == 3) {
      if (checkdate($date[1], $date[2], $date[0])) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  public function timeValidator($time) {
    $time = explode(':', $time);
    if (count($time) == 3) {
      if ($time[0] >= 0 && $time[0] <= 23) {
        if ($time[1] >= 0 && $time[1] <= 59) {
          if ($time[2] >= 0 && $time[2] <= 59) {
            return TRUE;
          } else {
            return FALSE;
          }
        } else {
          return FALSE;
        }
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }
}
