<?php

class PhoneProcess {
  const SEVEN = 7;
  const TEN = 10;
  const ELEVEN = 11;
  const GREEDY_ARRAY_LIMIT = 400;

  public function strictParse($str) {
    $words = $this->splitOnWords($str);
    $phones = [];
    foreach ($words as $word) {
      // удаляем все пробелы, "-", "(", ")", "+"
      // если то что получилось это число длиной 7, 10 или 11, то ок.
      // если нет, говорим что мы эту строку распарсить не можем
      $phone = preg_replace("/[+|\s|\-|\(|\)]/", '', $word);
      $phoneLen = strlen($phone);
      if (is_numeric($phone) && ($phoneLen === self::SEVEN || $phoneLen === self::TEN || $phoneLen === self::ELEVEN)) {
        $phones[] = $phone;
      } else {
        return false;
      }
    }

    foreach ($phones as $k => $phone) {
      $phones[$k] = $this->normalizePhone($phone);
    }

    return  array_unique($phones);
  }

  public function greedyParse($str) {
    $str = preg_replace("/[^0-9]/", ' ', trim($str));
    $numbers = preg_split("/\s+/", $str);

    if (count($numbers) > self::GREEDY_ARRAY_LIMIT) {
      throw new Exception('Cannot parse string with more that '.self::GREEDY_ARRAY_LIMIT.' numbers');
    }

    $phones = [];
    while(!empty($numbers)) {
      $phones = array_merge($phones, $this->getPhones($numbers));
      array_shift($numbers);
    }

    foreach ($phones as $k => $phone) {
      $phones[$k] = $this->normalizePhone($phone);
    }

    return array_unique($phones);;
  }

  private function getPhones($numbers) {
    $phones = [];
    $phone = "";
    foreach ($numbers as $number) {
      $phone .= $number;
      $phoneLen = strlen($phone);
      if ($phoneLen === self::SEVEN || $phoneLen === self::TEN || $phoneLen === self::ELEVEN) {
        $phones[] = $phone;
      }
      if ($phoneLen >  self::ELEVEN) {
        return $phones;
      }
    }

    return $phones;
  }
  /**
   * Считаем что разделителем слов могут быть  ",", ";" с любым кол-вом пробелов после
   * @param $str
   * @return array
   */
  public function splitOnWords($str) {
    return preg_split('!([,|;|]\s*)!', $str);
  }

  public function normalizePhone($phone){
    $phoneLen = strlen($phone);
    if ($phoneLen === self::SEVEN) {
      // это городской
      $phone = '8495'.$phone;
    } elseif ($phoneLen === self::TEN) {
      $phone = '8' . $phone;
    } elseif ($phoneLen === self::ELEVEN && substr($phone, 0, 1) === '7') {
      $phone = '8' . substr($phone, 0, 1);
    }
    return $phone;
  }
}