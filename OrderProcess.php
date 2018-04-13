<?php

class OrderProcess {
  private $db;
  private $emailClient;

  const MAX_INPUT_LENGTH = 2048;

  public function __construct($db, $emailClient)
  {
    $this->db = $db;
    $this->emailClient = $emailClient;
  }

  public function process($str) {
    if (strlen($str) > self::MAX_INPUT_LENGTH) {
      throw new Exception("Too long string");
    }

    // сразу сохраним заказ
    $id = $this->db->exec("INSERT INTO order SET phone='".$this->db->escape($str)."', order");


  }
}