<?php

require('vendor/autoload.php');

use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtension_Row;

$myclient = new ET_Client();
$dataextensionrow = new ET_DataExtension_Row();
$dataextensionrow->authStub = $myclient;
$dataextensionrow->Name = 'Semillas_Sindy';

// createDataExtensionRows($myclient, $dataextensionrow);
// updateDataExtensionRows($myclient, $dataextensionrow);
// deleteDataExtensionRows($myclient, $dataextensionrow);
retrieveDataExtensionRows($myclient, $dataextensionrow);

function retrieveDataExtensionRows($myclient, $dataextensionrow) {

    $dataextensionrow->authStub = $myclient;
    $dataextensionrow->Name = 'Semillas_Sindy';
    $dataextensionrow->props = array('UUID', 'Email', 'Account ID', 'Birthdate', 'First Name', 'Last Name', 'Mailing City', 'Mailing Country', 'Mailing State', 'Mailing Zip', 'Mobile Phone', 'AgeRange');
    $response = (array) $dataextensionrow->get();
    $response = $response['results'];

    echo "<table style='width: 100vw;'>";
    echo "<tr>";
        for ($i=0; $i < count($dataextensionrow->props); $i++) { 
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

}

function createDataExtensionRows($myclient, $dataextensionrow) {

    $dataextensionrow->props = array(
                            "UUID" => "0031X00503Vv178QAB",
                            "Email" => "sergivg10@gmail.com",
                            "Account ID" => "",
                            "Birthdate" => "7/29/2022 12:00:00 AM",
                            "First Name" => "Sergi",
                            "Last Name" => "Valenzuela",
                            "Mailing City" => "Barcelona",
                            "Mailing Country" => "ES",
                            "Mailing State" => "",
                            "Mailing Zip" => "08291",
                            "Mobile Phone" => "616616616",
                            "AgeRange" => "30",
                        );
    $results = $dataextensionrow->post();
    print_r($results);

}

function updateDataExtensionRows($myclient, $dataextensionrow) {

    $dataextensionrow->props = array(
                            "UUID" => "0031X00503Vv178QAB",
                            "Email" => "sergivg10000@gmail.com",
                            "Account ID" => "",
                            "Birthdate" => "7/29/2022 12:00:00 AM",
                            "First Name" => "Sergi",
                            "Last Name" => "Valenzuela",
                            "Mailing City" => "Barcelona",
                            "Mailing Country" => "ES",
                            "Mailing State" => "",
                            "Mailing Zip" => "08291",
                            "Mobile Phone" => "616616616",
                            "AgeRange" => "30",
                        );
    $results = $dataextensionrow->patch();
    print_r($results);

}

function deleteDataExtensionRows($myclient, $dataextensionrow) {

    $dataextensionrow->props = array("UUID" => "0031X00503Vv178QAB");
    $results = $dataextensionrow->delete();
    print_r($results);

}