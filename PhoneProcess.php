<?php

class PhoneProcess {
  const SEVEN = 7;
  const TEN = 10;
  const ELEVEN = 11;

  public function extractPhones($str) {
    // сначала попробуем разбить строку на слова
    $words = $this->splitOnWords($str);

    // Каждое слово будем разбивать на множесто возможных комбинаций телефонных номеров
    $numbers = [];
    foreach ($words as $word) {
      $partitions = $this->getPartitions($word);
      if (count($partitions) === 1) {
        $numbers = array_merge($numbers, reset($partitions));
      } else {
        // если какое-то слово не смогли однозначно разбить на номера,
        // просто скажем что не справились со всей строкой
        return false;
      }
    }

    // Преобразуем все номера к единому виду
    foreach ($numbers as $key => $number) {
      $numLen = strlen($number);
      if ($numLen === self::SEVEN) {
        // это городской
        $numbers[$key] = '8495'.$number;
      } elseif ($numLen === self::TEN) {
        $numbers[$key] = '8' . $number;
      } elseif ($numLen === self::ELEVEN && substr($number, 0, 1) === '7') {
        $numbers[$key] = '8' . substr($number, 0, 1);
      }
    }

    return $numbers;
  }

  /**
   * Считаем что разделителем слов могут быть  ",", ";", "/", "\" с любым кол-вом пробелов после
   * Самый плохой случай, когда для разделения и внтури телефонного номера
   * и между нимим используется только пробел
   * @param $str
   * @return array
   */
  public function splitOnWords($str) {
    return preg_split('!([,|;|/|\\\]\s*)!', $str);
  }

  public function getPartitions($str) {
    $str = preg_replace("/[^0-9]/", ' ', $str);
    $blocks = preg_split("/\s+/", $str);
    $digitsInBlocksCnt = $this->getDigitCount($blocks);
    $partitions = []; //здесь будет лежать массив разбиений на номера
    $partitionsStack = []; // это стэк неоконченных разбиений
    foreach ($this->getNumbers($blocks) as $number) {
      $partitionsStack[] = [$number];
    }
    while (!empty($partitionsStack)) {
      $partition = array_pop($partitionsStack);
      $digitInNumbersCnt = $this->getDigitCount($partition);

      // если кол-во цифр в нашем разбиении равно общем кол-ву в строке,
      // значит мы уже сделали окончательное разбиение
      if ($digitInNumbersCnt === $digitsInBlocksCnt) {
        $partitions[] = $partition;
        continue;
      }

      // если нет, продолжим разбивать дальше
      $restBlocks = $this->getBlocksAfterNDigits($blocks, $digitInNumbersCnt);
      foreach ($this->getNumbers($restBlocks) as $number) {
        $newPartition = $partition;
        $newPartition[] = $number;
        $partitionsStack[] = $newPartition;
      }
    }

    return $partitions;
  }

  /**
   * Возвращает массив чисел из $blocks без первых $n цифр
   * @param $blocks - массив чисел
   * @param int $n - сколько цифр надо скипнуть из $blocks
   * @return mixed
   */
  private function getBlocksAfterNDigits($blocks, $n) {
    $removedBlocksLength = 0;
    while ($removedBlocksLength < $n) {
      $removedBlocksLength += strlen(array_shift($blocks));
    }
    return $blocks;
  }

  private function getDigitCount($arrayOfNumbers) {
    $numbersDigitCnt = 0;
    foreach ($arrayOfNumbers as $number) {
      $numbersDigitCnt += strlen($number);
    }
    return $numbersDigitCnt;
  }

  /**
   * По массиву чисел $blocks возвращает массив телефонных номеров
   * которые удалось выделить из первых элементов $blocks
   * @param $blocks
   * @return array|bool
   */
  private function getNumbers($blocks){
    $numbers = [];
    $number = "";
    $numberLength = 0;
    while (
        $numberLength <= self::ELEVEN &&
        !empty($blocks)
    ) {
      $block = array_shift($blocks);
      $number .= $block;
      $blockLength = strlen($number);
      if ($blockLength === self::SEVEN || $blockLength === self::TEN || $blockLength === self::ELEVEN) {
        $numbers[] = $number;
      }
    }
    return $numbers;
  }
}