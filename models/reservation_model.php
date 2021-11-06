<?php
  class reservation_model {
    public $id;
    public $name;
    public $email;
    public $phoneNumber;
    public $NoP;
    public $date;
    public $time;
    public $description;

  public function __construct($id, $name, $email, $phoneNumber, $NoP, $date, $time, $description) {
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->phoneNumber = $phoneNumber;
    $this->NoP = $NoP;
    $this->date = $date;
    $this->time = $time;
    $this->description = $description;
  }
}
?>