<?php
namespace Src\Endpoints;

use Src\Methods\Collections;
use Src\Methods\PostsManager;

class PostsEndpoint {
  private $collections;
  private $posts;

  public function __construct($db) {
    $this->collections = new Collections($db);
    $this->posts = new PostsManager($db);
  }

  private function rejectParams() {
    $message = "400 Payload missing or incorrect";
    header("HTTP/1.1 $message");
    $body = [
      "ok" => false,
      "message" => $message,
    ];
    echo json_encode($body);
    exit();
  }

  public function show() {
    $result = $this->collections->getLatestPosts();
    header('HTTP/1.1 200 OK');
    if (json_encode($result)) echo json_encode($result);
  }

  
  public function create($params) {
    ['user_id' => $id, 'text' => $text] = $params;
    if (!strlen($id) || !is_int($id) || !strlen($text))  $this->rejectParams();
    $result = $this->posts->createPost($id, $text);
    header( $result ? 'HTTP/1.1 201 OK' : 'HTTP/1.1 400 Bad request');
    $body = [
      "ok" => boolval($result),
      "message" => $result ? 'New post created' : 'Error creating post',
    ];
    echo json_encode($body);
  }
  
  public function edit($id, $params) {
    ['text' => $text] = $params;
    if (!isset($id) || !is_int($id) || !strlen($text)) $this->rejectParams();
    $result = $this->posts->updatePost($id, $text);
    header( $result ? 'HTTP/1.1 200 OK' : 'HTTP/1.1 404 Not Found');
    $body = [
      "ok" => boolval($result),
      "message" => $result ? 'Post edited' : 'Incorrect post id',
    ];
    echo json_encode($body);
  }
  
  public function toggle($id, $bool) {
    if (!isset($id) || !is_int($id) || !isset($bool)) $this->rejectParams();
    $result = $this->posts->showPost($id, +$bool);
    header( $result ? 'HTTP/1.1 200 OK' : 'HTTP/1.1 404 Not Found');
    $message = $result ? 'Post '.($bool ? 'made visible' : 'hidden') : 'Incorrect post id';
    $body = [
      "ok" => $result,
      "message" => !isset($result) ? 'Nothing to change' : $message,
    ];
    echo json_encode($body);
  }

  public function delete($id) {
    $result = $this->posts->deletePost($id);
    header( $result ? 'HTTP/1.1 200 OK' : 'HTTP/1.1 404 Not Found');
    $message = $result ? 'Post deleted' : 'Nothing to delete';
    $body = [
      "ok" => boolval($result),
      "message" => $message,
    ];
    echo json_encode($body);
  }
}
  