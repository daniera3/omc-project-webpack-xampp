<?php


$error = new Katzgrau\KLogger\Logger(__DIR__ . DS . '..' . DS . 'logs', Psr\Log\LogLevel::WARNING, array(
    'filename' => 'error',
    'extension' => 'log'
));

$request = new Katzgrau\KLogger\Logger(__DIR__ . DS . '..' . DS . 'logs', Psr\Log\LogLevel::INFO, array(
    'filename' => 'request',
    'extension' => 'log'
));


$response_pass = new Katzgrau\KLogger\Logger(__DIR__ . DS . '..' . DS . 'logs', Psr\Log\LogLevel::INFO, array(
    'filename' => 'response_pass',
    'extension' => 'log'
));

$response_fail = new Katzgrau\KLogger\Logger(__DIR__ . DS . '..' . DS . 'logs', Psr\Log\LogLevel::WARNING, array(
    'filename' => 'response_fail',
    'extension' => 'log'
));

$info = new Katzgrau\KLogger\Logger(__DIR__ . DS . '..' . DS . 'logs', Psr\Log\LogLevel::DEBUG, array(
    'filename' => 'info',
    'extension' => 'log'
));

function log_info(string $massage, ...$all_the_others)
{
    global $info;
    $info->debug($massage, $all_the_others);

}



function log_request(string $massage, ...$all_the_others)
{
    global $request;
    $request->info($massage, $all_the_others);

}


function log_response_pass(string $massage, ...$all_the_others)
{
    global $response_pass;
    $response_pass->info($massage, $all_the_others);

}

function log_response_fail(string $massage, ...$all_the_others)
{
    global $response_fail;
    $response_fail->warning($massage, $all_the_others);

}



function log_error(string $massage, ...$all_the_others)
{
    global $error;

    $error->critical($massage, $all_the_others);
}

