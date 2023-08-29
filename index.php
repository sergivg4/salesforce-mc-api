<?php

require('vendor/autoload.php');

use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtension_Row;

$myclient = new ET_Client();
$dataextensionrow = new ET_DataExtension_Row();
$dataextensionrow->authStub = $myclient;
$dataextensionrow->Name = 'Test_Rosa';
$dataextensionrow->props = array('First Name', 'Last Name', 'Email');
$response = (array) $dataextensionrow->get();
$response = $response['results'];

echo "<table>";
echo "<tr>";
    for ($i=0; $i < 3; $i++) { 
        echo "<th style='text-align: start;'>" . $response[0]->Properties->Property[$i]->Name . "</th>";
    }
echo "</tr>";
foreach ($response as $key => $value) {
    foreach ($response[$key]->Properties as $value2) {
        echo "<tr>";
        foreach ($value2 as $key => $value3) {
            echo "<td>" . $value3->Value . "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
