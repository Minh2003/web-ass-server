<?php
  class user_model {
    public $id;
    public $email;
    public $password;
    public $username;
    public $phoneNumber;
    public $avatar;
    public $manager;
    
    public function __construct($id, $email, $password, $username, $phoneNumber, $avatar, $manager) {
      $this->id = $id;
      $this->email = $email;
      $this->password = $password;
      $this->username = $username;
      $this->phoneNumber = $phoneNumber;
      $this->avatar = $avatar;
      $this->manager = $manager;
    }
  }

?>