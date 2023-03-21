<?php

include_once "csvdecode.php";
use br0kenpixel\CsvDecoder;

echo "Starting validity check...\n";

$decoder = new CsvDecoder();
$decoder->parse_file("source/headerMenu.csv");
echo "Decoder initialized\n";

echo "Headers:\n";
var_dump($decoder->getHeaders());

echo "Fetching entries...\n";
while($decoder->available()) {
    $entry = $decoder->fetchOne();
    foreach($decoder->getHeaders() as $header) {
        echo $entry[$header] . "\n";
    }
    echo "\n";
}

$decoder->destroy();
echo "Decoder closed.\n";

?>