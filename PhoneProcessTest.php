<?php

include "PhoneProcess.php";

$tests = [
    [
        'input'=>'8 (905) 123 45 67',
        'expectedStrict' =>["89051234567"],
        'expectedGreedy' => [
            0 => '84958905123',
            1 => '89051234567',
            3 => '84951234567',
        ]
    ],

    [
        'input'=>'9051234567',
        'expectedStrict' =>["89051234567"],
        'expectedGreedy' => [
            0 => '89051234567',
        ]
    ],

    [
        'input'=>'123-45-67',
        'expectedStrict' =>["84951234567"],
        'expectedGreedy' => [
            0 => '84951234567',
        ]
    ],

    [
        'input'=>'495 123-45-67, 8 (905) 123 45 67, 9051234567',
        'expectedStrict' =>["84951234567", "89051234567"],
        'expectedGreedy' =>[
            0 => '84951234567',
            1 => '49512345678',
            3 => '12345678905',
            4 => '45678905123',
            5 => '67890512345',
            6 => '84958905123',
            7 => '89051234567',
        ]
    ],

    [
        'input'=>'123-45-67 доб. 2200, 8 (905) 123 45 67, 9051234567',
        'expectedStrict' =>false,
        'expectedGreedy' =>[
            0 => '84951234567',
            1 => '12345672200',
            2 => '84956722008',
            3 => '86722008905',
            4 => '22008905123',
            5 => '84958905123',
            6 => '89051234567',
        ]
    ],

    [
        'input'=>'123-45-67 доб. 2200 спросить Машу, 8 (905) 222 3344 - звонить до 18, 9051234567, до 14го числа',
        'expectedStrict' =>false,
        'expectedGreedy' =>[
            0 => '84951234567',
            1 => '12345672200',
            2 => '84956722008',
            3 => '86722008905',
            4 => '22008905222',
            5 => '84958905222',
            6 => '89052223344',
            8 => '84952223344',
            9 => '89051234567',
        ]
    ],
];

foreach ($tests as $test) {
  runTest($test['input'], $test['expectedStrict'], $test['expectedGreedy']);
}

/**
 * @param string $input
 * @param array $expected
 */
function runTest($input, $expectedStrict, $expectedGreedy) {
  $pp = new PhoneProcess();
  echo $input."\n";
  $phones = $pp->strictParse($input);
  echo " - strict - ".(json_encode($phones) === json_encode($expectedStrict) ? "OK" : var_export($phones, true));
  echo "\n";
  $phones = $pp->greedyParse($input);
  echo " - greedy - ".(json_encode($phones) === json_encode($expectedGreedy) ? "OK" : var_export($phones, true));
  echo "\n";
}
