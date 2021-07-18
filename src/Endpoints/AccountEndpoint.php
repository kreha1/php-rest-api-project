<?php
namespace Src\Endpoints;

use Src\Methods\Collections;
use Src\Methods\AccountManager;

class AccountEndpoint {
  private $account;

  public function __construct($db) {
    $this->account = new AccountManager($db);
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
  private function handleResponse($result, $null, $true, $false) {
    return !isset($result) ? $null: ($result ? $true : $false);
  }

  
  public function create($params) {
    ['email' => $email, 'password' => $password] = $params;
    if (!strlen($email) || !strlen($password)) $this->rejectParams();
    $result = $this->account->createAccount(strval($email), $password);
    $code = !isset($result) ? 406 : ($result ? 200 : 400);
    $message = $this->handleResponse($result, "Not Acceptable", "OK", "Bad Request");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Email addres already in use", "Account created", "Error creating account"),
    ];
    echo json_encode($body);
  }

  
  public function password($params) {
    ['id' => $id, 'password' => $password] = $params;
    if (!isset($id) || !is_int($id) || !strlen($password)) $this->rejectParams();
    $result = $this->account->updatePassword((int) $id, $password);
    $code = !isset($result) ? 406 : ($result ? 200 : 400);
    $message = $this->handleResponse($result, "Not Acceptable", "OK", "Bad Request");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Account doesn't exist", "Password changed", "Error changing password"),
    ];
    echo json_encode($body);
  }
  
  public function close($params) {
    ['id' => $id] = $params;
    if (!isset($id) || !is_int($id)) $this->rejectParams();
    $result = $this->account->closeAccount((int) $id);
    $code = !isset($result) ? 406 : ($result ? 200 : 404);
    $message = $this->handleResponse($result, "Not Acceptable", "OK", "Not Found");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Account doesn't exist", "Account closed", "Account already closed"),
    ];
    echo json_encode($body);
  }

  
  public function auth($params) {
    ['email' => $email,'password' => $password] = $params;
    if (!isset($email) || !isset($password) || !strlen($email) || !strlen($password)) $this->rejectParams();
    $result = $this->account->authorize($email, $password);
    $code = !isset($result) ? 406 : ($result ? 200 : 404);
    $message = $this->handleResponse($result, "Not Acceptable", "OK", "Not Found");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Account doesn't exist", "User authorized", "Invalid credentials"),
    ];
    echo json_encode($body);
  }

  public function activate($params) {
    ['id' => $id] = $params;
    if (!isset($id) || !is_int($id)) $this->rejectParams();
    $result = $this->account->activateAccount((int) $id);
    $code = !isset($result) ? 406 : ($result ? 200 : 404);
    $message = $this->handleResponse($result, "Not Acceptable", "OK", "Not Found");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Account doesn't exist", "Account activated", "Account already activated"),
    ];
    echo json_encode($body);
  }
  
  public function delete($params) {
    ['id' => $id] = $params;
    if (!isset($id) || !is_int($id)) $this->rejectParams();
    $result = $this->account->scheduleDeletion((int) $id);
    $code = !isset($result) ? 404 : ($result ? 200 : 400);
    $message = $this->handleResponse($result, "Not Found", "OK", "Bad Request");
    header("HTTP/1.1 $code $message");
    $body = [
      "ok" => $result,
      "message" => $this->handleResponse($result, "Account doesn't exist", "Account scheduled for deletion", "Account already scheduled for deletion"),
    ];
    echo json_encode($body);
  }
}
  