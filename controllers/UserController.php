<?php
  namespace Controllers;

  use blog_model;
  use dish_model;
  use reservation_model;
  use Db;
class UserController {
  public function update($user_id, $form) {

  }

  public function create_comment($comment) {

  }

  public function delete_comment($comment_id) {
    
  }

  public function getBlogAll() {
    $db = Db::getInstance();

    $list = [];
    $sql = 'SELECT * FROM blog';
    $result = mysqli_query($db, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);
    }

    echo json_encode($list);
  }

  public function getBlogDetail($param) {
    $db = Db::getInstance();

    $id = substr($param, 1, -1);
    $list = [];

    $sql = "SELECT * FROM blog where id = $id";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new blog_model($row['id'], $row['title'], $row['content'], $row['image'], $row['date']);
    }

    echo json_encode($list);
  }

  public function getMenu() {
    $db = Db::getInstance();

    $list = [];
    $sql = 'SELECT * FROM dish';
    $result = mysqli_query($db, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $list[] = new dish_model($row['id'], $row['name'], $row['description'], $row['image']);
    }

    echo json_encode($list);
  }

  public function reservation() {
    $db = Db::getInstance();

    $date = time();
    $description = $_POST['description'];
    $NoP = $_POST['NoP'];

    $sql = "insert into reservation (description, NoP) values ('$description', '$NoP')";
    $result = mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $result = new reservation_model($id, $date, $description, $NoP);
    echo json_encode($result);
  }
}
?>