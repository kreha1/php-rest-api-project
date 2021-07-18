<?php
namespace Src\Endpoints;

use Src\Methods\Collections;

class UsersEndpoint {
  private $collections;

  public function __construct($db) {
    $this->collections = new Collections($db);
  }

  public function showActive() {
    $result = $this->collections->getActiveUsers();
    header('HTTP/1.1 200 OK');
    if (json_encode($result)) echo json_encode($result);
  }

  public function showUserPosts($id) {
    $result = $this->collections->getUserPosts($id);
    header('HTTP/1.1 200 OK');
    if (json_encode($result)) echo json_encode($result);
  }
}
