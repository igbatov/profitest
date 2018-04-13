<?php

include "PhoneProcess.php";

$tests = [
    ['input'=>'8 (905) 123 45 67', 'expected' =>["89051234567"]],
    ['input'=>'9051234567', 'expected' =>["89051234567"]],
    ['input'=>'123-45-67', 'expected' =>["84951234567"]],
    ['input'=>'495 123-45-67, 8 (905) 123 45 67, 9051234567', 'expected' =>["84951234567", "89051234567", "89051234567"]],
];

foreach ($tests as $test) {
  runTest($test['input'], $test['expected']);
}

/**
 * @param string $input
 * @param array $expected
 */
function runTest($input, $expected) {
  $pp = new PhoneProcess();
  echo $input." - ";
  $partitions = $pp->extractPhones($input);
  echo json_encode($partitions) === json_encode($expected) ? "OK" : var_export($partitions, true);
  echo "\n";
}
