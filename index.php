<?php

require('vendor/autoload.php');

use FuelSdk\ET_Client;
use FuelSdk\ET_DataExtension_Column;

$myclient = new ET_Client();
$dataextensioncolumn = new ET_DataExtension_Column();
$dataextensioncolumn->authStub = $myclient;
$response = $dataextensioncolumn->get();
print_r($response);