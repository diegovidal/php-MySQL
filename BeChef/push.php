<?php

require 'Parse/autoload.php';

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
use Parse\ParseClient;

$app_id = "4RJ8sqEgu2oM4dZUDM9NyPQbIy93iOI8PnfriK64";
$rest_key = "lYBiOjYtMvSIMvVPNKmh8vY5xjVmEf4HvYVN2yAK";
$master_key = "OhWOePtr2rz7otGktFMdjO6tqlxm63EdX1UOZ0UO";

ParseClient::initialize($app_id, $rest_key, $master_key);

function sendPush($message){

$data = array("alert" => $message, "badge" => "Increment");

// Push to Channels
ParsePush::send(array(
    "channels" => ["Everyone"],
    "data" => $data
));

// // Push to Query
// $query = ParseInstallation::query();
// $query->equalTo("design", "rad");
// ParsePush::send(array(
//     "where" => $query,
//     "data" => $data
// ));

}

?>