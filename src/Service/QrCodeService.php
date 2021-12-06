<?php

// namespace App\Service;

// use Endroid\QrCode\Color\Color;
// use Endroid\QrCode\Label\Margin\Margin;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\Builder\BuilderInterface;
// use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;

// class QrCodeService
// {



//     protected $builder;

//     public function __construct(BuilderInterface $builder)
//     {
//         $this->builder = $builder;
//     }

//     public function qrcode($query)
//     {



//         $url = "https://swift-restaurant.herokuapp.com/?table=";

//         $lableString = 'Table ' . $query;

//         $path = dirname(__DIR__, 2) . '/public/img/';

//         // set qrcode
//         $result = $this->builder
//             ->data($url . $query)
//             ->encoding(new Encoding('UTF-8'))
//             ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
//             ->size(400)
//             ->margin(10)
//             ->labelText($lableString)
//             ->labelAlignment(new LabelAlignmentCenter())
//             ->labelMargin(new Margin(25, 5, 5, 5))
//             ->backgroundColor(new Color(221, 158, 3))

//             ->build();

//         //generate name
//         $namePng = 'table_' . $query  . uniqid('_', '') . '.png';

//         //Save img png
//         $result->saveToFile($path . 'qr-code/' . $namePng);


//         return $result->getDataUri();
//     }
// }
