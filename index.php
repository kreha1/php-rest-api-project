<?php
require "./bootstrap.php";
use Src\Endpoints\UsersEndpoint;
use Src\Endpoints\PostsEndpoint;
use Src\Endpoints\AccountEndpoint;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = array_filter(explode('/', $path), fn($x) => !is_NULL($x) && $x !== '');

function reject($code = 404) {
  switch ($code) {
    case 405:
      $message = "405 Method Not Allowed";
      break;
    default:
      $message = "404 Not Found";
      break;
  }
  header("HTTP/1.1 $message");

  $body = [
    "ok" => false,
    "message" => $message,
  ];
  echo json_encode($body);
  exit();
}

if (count($uri) < 3) {
  reject();
}

$method = $_SERVER["REQUEST_METHOD"];

$json = file_get_contents('php://input');
$payload = json_decode($json, true);

$isGet = $method === 'GET';
$isPut = $method === 'PUT';
$isPost = $method === 'POST';
$isPatch = $method === 'PATCH';
$isDelete = $method === 'DELETE';

function checkEndpoint($id, $name, $max = NULL) {
  $max = isset($max) ? $max : $id;
  global $uri;
  return isset($uri[$id]) && ($uri[$id] === $name) && (count($uri) === $max);
}

switch ($uri[3]) {
  case 'account':
    if (count($uri) === 3) reject();
    $account = new AccountEndpoint($dbConnection);
    if (checkEndpoint(4, 'create')) {
      if (!$isPost) reject(405);
      $account->create($payload);
    } else if (checkEndpoint(4, 'password')) {
      if (!$isPut) reject(405);
      $account->password($payload);
    } else if (checkEndpoint(4, 'activate')) {
      if (!$isPatch) reject(405);
      $account->activate($payload);
    } else if (checkEndpoint(4, 'auth')) {
      if (!$isPost) reject(405);
      $account->auth($payload);
    } else if (checkEndpoint(4, 'close')) {
      if (!$isPatch) reject(405);
      $account->close($payload);
    } else if (checkEndpoint(4, 'delete')) {
      if (!$isPost) reject(405);
      $account->delete($payload);
    } else reject();
    
    break;
  case 'users':
    if (!$isGet) reject(405);
    if (count($uri) === 3) {
      $users = new UsersEndpoint($dbConnection);
      $users->showActive();
    } else if (isset($uri[4])) {
      $users = new UsersEndpoint($dbConnection);
      $users->showUserPosts((int) $uri[4]);
    } else reject();
    break;
  case 'posts':
    $posts = new PostsEndpoint($dbConnection);
    if (count($uri) === 3) {
      if (!$isGet) reject(405);
      $posts->show();
    } else if (checkEndpoint(4, 'new')) {
      if (!$isPost) reject(405);
      $posts->create($payload);
    } else if (checkEndpoint(4, 'edit', 5)) {
      if (!$isPatch) reject(405);
      $posts->edit((int) $uri[5], $payload);
    } else if (checkEndpoint(4, 'show', 5)) {
      if (!$isPatch) reject(405);
      $posts->toggle((int) $uri[5], 1);
    } else if (checkEndpoint(4, 'hide', 5)) {
      if (!$isPatch) reject(405);
      $posts->toggle((int) $uri[5], 0);
    } else if (checkEndpoint(4, 'delete', 5)) {
      if (!$isDelete) reject(405);
      $posts->delete((int) $uri[5]);
    } else reject();
    break;
  default:
    reject();
    break;
}
/**
 * 
 * $userId = NULL;
if (isset($uri[1])) {
    $userId = (int) $uri[1];
}
// pass the request method and user ID to the Users:
$response = new Users($dbConnection, $method, $userId);
$response->processRequest();
 * 
 */






?>
