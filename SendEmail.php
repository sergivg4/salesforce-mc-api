<?php
require('vendor/autoload.php');

use FuelSdk\ET_Client;
use FuelSdk\ET_PostRest;
use FuelSdk\ET_DataExtension;

$myclient = new ET_Client(true, true);

//Crear Fecha
$now = new DateTime();
$now->add(new DateInterval('PT2H10M'));
$fecha = $now->format('Ymd');
$hora = $now->format('Hi00');

//Crear html mail
$html_final = createHtml();

//Crear Mail en MC
$nameMail = "Test_Email_Sergi_" . $fecha . "_" . $hora;
$subject = "Test";
$email= crateEmail($myclient,$nameMail,$html_final,$subject);
$emailKey = $email->results->customerKey;
$emailLegacyID= $email->results->legacyData->legacyId;

//Create Data Extension
$nameDE="Test_Email_Sergi_" . $hora . "_" . $fecha;
$data_ext = createDataExtension($myclient,$nameDE);
$deObjectID=$data_ext[0]->NewObjectID;

//Add Users
$users_sample = array ( 
    'items' => array (
        array (
            'UUID' => '0033X00003evNrHQAU',
            'email' => 'sergi.valenzuela.externo@penguinrandomhouse.com',
            'Nombre' => 'Sergi',
        ),
        array (
            'UUID' => '0033X00003evM5KQAU',
            'Email' => 'sergivg10@gmail.com',
            'Nombre' => 'Sergi',
        ),
        array (
            'UUID' => '0033X00003VdxgCQAR',
            'Email' => 'sergivalenzuela@capitole-consulting.com',
            'Nombre' => 'Sergi',
        ),
    ),
);
$result = addUsersDE($myclient, $nameDE, $users_sample);

//Crear Evento
$nameEvent="Test_Email_Sergi_".$fecha."_".$hora;
$event= createEvent($myclient,$nameDE,$nameEvent,$deObjectID);       
$eventID=$event->results->id;

//Crear Journey
$nameJourney = "Test_Email_Sergi_".$fecha."_".$hora;;
$senderProfileID="a11170b2-4a78-eb11-b834-f40343c97c58";
$deliveryProfileID="a11170b2-4a78-eb11-b834-f40343c97c58";
$journey = createJourney($myclient,$nameJourney,$nameEvent,$eventID,$nameMail,$emailKey,$emailLegacyID,$senderProfileID, $deliveryProfileID,$subject);

//Activar Journey
$journeyID=$journey->results->id;
$resultActivate= activateJourney($myclient,$journeyID);
print_r($resultActivate);


function createHtml() {

    $html = "<p>Hola</p>";

    $html_final = preg_replace("/[\r\n|\n|\r]+/", "", $html);
    $html_final = str_replace("                        ", "", $html_final);
    $html_final = str_replace("    ", "", $html_final);
    $html_final = preg_replace("/[\r\n|\n|\r]+/", "", $html_final);

    return $html_final;
}

function crateEmail($myclient,$name,$html,$subject) {
        
    $deURL = "https://mclt7dkpvhsd7h170qfjl6087h11.rest.marketingcloudapis.com/asset/v1/content/assets";
    
    $deRows = json_decode('{
        "name": "'.$name.'",
        "category": { "id": 7156 }, 
        "channels": {
        "email": true,
        "web": false
        },
        "views": {
        "html": {
            "content": "'.$html.'"
        },
        "text": {},
        "subjectline": { "content": "'.$subject.'"},
        "preheader": {}
        },
        "assetType": {
        "name": "htmlemail",
        "id": 208
        }
    }');
    
    $restDE = new ET_PostRest($myclient ,$deURL, $deRows,$myclient->getAuthToken());

    return $restDE;
      
}

function createDataExtension($myclient,$nameDE) {
    
    $postDE = new ET_DataExtension();
    $postDE->authStub = $myclient;
    $postDE->props = array(
                    "Name" => $nameDE,
                    "CustomerKey" => $nameDE,
                    "CategoryID"=> 18088, // Id de Test
                    "IsSendable" => "true",
                    "SendableDataExtensionField" => array(
                        'Name' => 'Email',
                        'Value' => NULL
                        ),
                    "SendableSubscriberField" => array(
                        'Name' => 'Subscriber Key',
                        'Value' => NULL
                        )
                    );
    $postDE->columns = array();
    $postDE->columns[] = array("Name" => "UUID", "FieldType" => "Text", "IsPrimaryKey" => "true","MaxLength" => "100", "IsRequired" => "true");
    $postDE->columns[] = array("Name" => "Email", "FieldType" => "EmailAddress");
    $postDE->columns[] = array("Name" => "Nombre", "FieldType" => "Text");
    $postResult = $postDE->post();

    // print_r($postResult->results);

    return $postResult->results;

}

function addUsersDE($myclient,$nameDE, $users) {

    $deURL = 'https://mclt7dkpvhsd7h170qfjl6087h11.rest.marketingcloudapis.com/data/v1/async/dataextensions/key:'.$nameDE.'/rows/';
    
    $restDE = new ET_PostRest($myclient ,$deURL, $users,$myclient->getAuthToken());

    print_r($restDE);
    
}

function createEvent($myclient,$nameDE,$nameEvent,$deObjectID) {

    $now = new DateTime();
    $now->add(new DateInterval('PT2H10M'));
    $fecha = $now->format('Y-m-d');
    $hora = $now->format('H:i:00');

    $deURL ="https://mclt7dkpvhsd7h170qfjl6087h11.rest.marketingcloudapis.com/interaction/v1/eventDefinitions";

    $jsonEvent= json_decode('{
        "type": "EmailAudience",
        "name": "'.$nameEvent.'",
        "eventDefinitionKey": "'.$nameEvent.'",
        "mode": "Production",
        "dataExtensionName": "'.$nameDE.'",
        "dataExtensionId": "'.$deObjectID.'",
        "sourceApplicationExtensionId": "97e942ee-6914-4d3d-9e52-37ecb71f79ed",
        "filterDefinitionId": "00000000-0000-0000-0000-000000000000",
        "filterDefinitionTemplate": "",
        "iconUrl": "/images/icon-data-extension.svg",
        "arguments": {
            "serializedObjectType": 3,
            "eventDefinitionKey": "'.$nameEvent.'",
            "dataExtensionId": "'.$deObjectID.'",
            "criteria": "",
            "useHighWatermark": false
        },
        "configurationArguments": {
            "unconfigured": false
        },
        "metaData": {
            "criteriaDescription": "",
            "scheduleFlowMode": "runOnce",
            "runOnceScheduleMode": "onSchedule"
        },
        "schedule": {
            "startDateTime": "'.$fecha.'T'.$hora.'",
            "endDateTime": "'.$fecha.'T'.$hora.'",
            "timeZone": "Romance Standard Time",
            "occurrences": 1,
            "endType": "Occurrences",
            "frequency": "Daily",
            "recurrencePattern": "Interval",
            "interval": 1
        },
        "interactionCount": 1,
        "isVisibleInPicker": false,
        "isPlatformObject": false,
        "category": "Audience",
        "publishedInteractionCount": 0,
        "disableDEDataLogging": false
    }');
    
    $restDE = new ET_PostRest($myclient ,$deURL, $jsonEvent,$myclient->getAuthToken());

    return $restDE;
    
}

function createJourney($myclient,$nameJourney,$nameEvent,$eventID,$nameMail,$emailKey,$emailLegacyID,$senderProfileID, $deliveryProfileID, $subject){

    $deURL="https://mclt7dkpvhsd7h170qfjl6087h11.rest.marketingcloudapis.com/interaction/v1/interactions";
    $jsonJourney=json_decode('
    {
      "key": "'.$nameJourney.'",
      "name": "'.$nameJourney.'",
      "description": "",
      "version": 1,
      "workflowApiVersion": 1.0,
      "activities": [
          {
              "key": "'.$emailKey.'",
              "name": "'.$nameMail.'",
              "description": "",
              "type": "EMAILV2",
              "outcomes": [
                  {
                      "key": "'.$emailKey.'",
                      "arguments": {},
                      "metaData": {
                          "invalid": false
                      }
                  }
              ],
              "arguments": {},
              "configurationArguments": {
                  "triggeredSend": {
                      "autoAddSubscribers": true,
                      "autoUpdateSubscribers": true,
                      "bccEmail": "",
                      "ccEmail": "",
                      "created": {},
                      "domainExclusions": [],
                      "dynamicEmailSubject": "'.$subject.'",
                      "emailId": '.$emailLegacyID.',
                      "emailSubject": "'.$subject.'",
                      "exclusionFilter": "",
                      "isSalesforceTracking": true,
                      "isMultipart": true,
                      "isSendLogging": true,
                      "isStoppedOnJobError": false,
                      "modified": {},
                      "preHeader": "",
                      "priority": 4,
                      "sendClassificationId": "567927b4-ec4b-ea11-b81b-f40343c95990",
                      "throttleOpens": "1/1/0001 12:00:00 AM",
                      "throttleCloses": "1/1/0001 12:00:00 AM",
                      "deliveryProfileId": "'.$deliveryProfileID.'",
                      "senderProfileId": "'.$senderProfileID.'",
                      "isTrackingClicks": true,
                      "publicationListId": 46
                  },
                  "applicationExtensionKey": "jb-email-activity",
                  "isModified": true
              },
              "metaData": {
                  "icon": "https://jb-email-activity.s50.marketingcloudapps.com/img/email-icon.svg",
                  "iconSmall": "https://jb-email-activity.s50.marketingcloudapps.com/img/email-icon.svg",
                  "category": "message",
                  "version": "1.0",
                  "statsContactIcon": "",
                  "original_icon": "/img/email-icon.svg",
                  "original_iconSmall": "/img/email-icon.svg",
                  "sections": {},
                  "isConfigured": true
              },
              "schema": {
                  "arguments": {
                      "requestID": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "Out",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "messageKey": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "Out",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "emailSubjectDataBound": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": true,
                          "access": "Hidden"
                      },
                      "contactId": {
                          "dataType": "Number",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "contactKey": {
                          "dataType": "Text",
                          "isNullable": false,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "emailAddress": {
                          "dataType": "Text",
                          "isNullable": false,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "sourceCustomObjectId": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "sourceCustomObjectKey": {
                          "dataType": "LongNumber",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "fieldType": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "eventData": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "obfuscationProperties": {
                          "dataType": "Text",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      },
                      "customObjectKey": {
                          "dataType": "LongNumber",
                          "isNullable": true,
                          "direction": "In",
                          "readOnly": true,
                          "access": "Hidden"
                      },
                      "definitionInstanceId": {
                          "dataType": "Text",
                          "isNullable": false,
                          "direction": "In",
                          "readOnly": false,
                          "access": "Hidden"
                      }
                  }
              }
          }
      ],
      "triggers": [
          {
              "key": "TRIGGER",
              "name": "TRIGGER",
              "description": "",
              "type": "EmailAudience",
              "outcomes": [],
              "arguments": {},
              "configurationArguments": {},
              "metaData": {
                  "eventDefinitionId": "'.$eventID.'",
                  "eventDefinitionKey": "'.$nameEvent.'",
                  "chainType": "None",
                  "configurationRequired": false,
                  "iconUrl": "/images/icon-data-extension.svg",
                  "title": "Data Extension",
                  "entrySourceGroupConfigUrl": "jb:///data/entry/audience/entrysourcegroupconfig.json"
              }
          }
      ],
      "goals": [],
      "exits": [],
      "stats": {
          "currentPopulation": 0,
          "cumulativePopulation": 0,
          "metGoal": 0,
          "metExitCriteria": 0,
          "goalPerformance": 0.0
      },
      "entryMode": "MultipleEntries",
      "definitionType": "Quicksend",
      "channel": "email",
      "defaults": {
          "email": [
              "{{Event.'.$nameEvent.'.\"Email\"}}"
          ],
          "properties": {
              "analyticsTracking": {
                  "enabled": false,
                  "analyticsType": "google",
                  "urlDomainsToTrack": []
              }
          }
      },
      "metaData": {
          "isScheduleSet": true
      },
      "executionMode": "Production",
      "categoryId": 28252,
      "status": "Draft",
      "scheduledStatus": "Draft"
  }');
    
  $restDE = new ET_PostRest($myclient ,$deURL, $jsonJourney,$myclient->getAuthToken());
  return $restDE;
  
}

function activateJourney($myclient,$journeyID) {

        $deURL="https://mclt7dkpvhsd7h170qfjl6087h11.rest.marketingcloudapis.com/interaction/v1/interactions/publishAsync/".$journeyID."?versionNumber=1";
        $restDE = new ET_PostRest($myclient ,$deURL,array(),$myclient->getAuthToken());
        return $restDE;
}