<?php

require_once '../vendor/autoload.php';

use DevCoder\DotEnv;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;

$absolutePathToEnvFile = '../.env';

(new DotEnv($absolutePathToEnvFile))->load();

$ticket_id = false;
$error_msg = false;

$client = new Client([
    'url'        => getenv('ZAMMAD_URL'),
    'http_token' => getenv('ZAMMAD_TOKEN'),
    // 'timeout'       => 15,                  // Sets timeout for requests, defaults to 5 seconds, 0: no timeout
    //'debug'         => true,                // Enables debug output
]);

$ticket_data = [
    'group_id'    => 1, // Group 'PrevHelp - Allgemein'
    'priority_id' => 2,
    'state_id'    => 1,
    'title'       => htmlspecialchars($_POST['subject']),
    'customer_id' =>  "guess:" . htmlspecialchars($_POST['email']),
    'type'        => 'email',
    'article'     => [
        'type_id' => 1,
        'from'    =>  htmlspecialchars($_POST['name']).' <'. htmlspecialchars($_POST['email']).'>',
        'to'      => 'PrevHelp Support',
        'subject' =>  htmlspecialchars($_POST['subject']),
        'body'    =>  htmlspecialchars($_POST['message']),
        'content_type' => 'text/html',
        'type'    => 'email',
        'sender'  => 'Customer',
    ],
];

$ticket = $client->resource(ResourceType::TICKET);
$ticket->setValues($ticket_data);
$ticket->save();

header('Location: https://'. $_GET['return']);
exit;

// not used at the moment...
/*
if ($ticket->hasError()) {
    $error_msg = $ticket->getError();
} else {
    $ticket_id = $ticket->getValue('number');
}
*/
