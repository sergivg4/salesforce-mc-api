
<?php

require '../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use FuelSdk\ET_Client;

$clientId = '4e7d6a2vw3crtn56olxl6y9e';
$clientSecret = 'TMAZe5PYspD9SAbAY9kcyla2';
$accessToken = get_token($clientId, $clientSecret);

function get_token($clientId, $clientSecret) {
    $authUrl = 'https://mclt7dkpvhsd7h170qfjl6087h11.auth.marketingcloudapis.com/v2/token';

    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => $clientId,
        'client_secret' => $clientSecret
    ];

    $ch = curl_init($authUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Error en la solicitud: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);
        $accessToken = $responseData['access_token'];
        echo 'Access Token: ' . $accessToken . '<br><br>';
        return $accessToken;
    }

    curl_close($ch);
}

try {
    $myclient = new ET_Client(
        true,
        true, 
        array(
                'appsignature' => 'none',
                'clientid' => '',
                'clientsecret' => '',
                'defaultwsdl' => 'https://webservice.exacttarget.com/etframework.wsdl',
                'xmlloc' => './ExactTargetWSDL.xml',
                'baseUrl' => '',
                'baseAuthUrl' => '',
                'baseSoapUrl' => '',
                'useOAuth2Authentication' => true,
                'applicationType' => 'server',
                //'scope' => 'documents_and_images_write data_extensions_write journeys_read email_write saved_content_write',
        )
      );
    // $myclient->setAuthToken($accessToken);
    
} catch (Exception $e) {
    echo '<br><br>Excepción: ' . $e->getMessage();
}
