<?php

function prepareBackupDatabase()
{
    global $config;

    $host = $config['mongodb']['host'];
    $port = $config['mongodb']['port'];

    exec("mongo " . $host . ":" . $port . "/" . $config['mongodb']['database'] ."_backup " . __DIR__ . "/scripts/clearDb.js");
    exec("mongo " . $host . ":" . $port . "/" . $config['mongodb']['database'] ."_backup " . __DIR__ . "/scripts/prepareBackup.js");
}

function clearDatabase()
{
    global $config;

    $host = $config['mongodb']['host'];
    $port = $config['mongodb']['port'];

    exec("mongo " . $host . ":" . $port . "/" . $config['mongodb']['database'] . " " . __DIR__ . "/scripts/clearDb.js");
}

function restoreDatabase()
{
    global $config;

    $host = $config['mongodb']['host'];
    $port = $config['mongodb']['port'];

    exec("mongo " . $host . ":" . $port . "/" . $config['mongodb']['database'] ." " . __DIR__ . "/scripts/copyDb.js");
}