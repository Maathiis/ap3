<?php

$DB_SERVER = getenv("MVC_SERVER") ?: "192.168.122.1";
$DB_DATABASE = getenv("MVC_DB") ?: "media-tout";
$DB_USER = getenv("MVC_USER") ?: "mperrault"; 
$DB_PASSWORD = getenv("MVC_TOKEN") ?: "bSKVH(lwj,@LfMwX=hH2";
$DEBUG = getenv("MVC_DEBUG") ?: true;
$URL_VALIDATION = getenv("MVC_URL_VALIDATION") ?: "http://localhost:9000/valider-compte/";
$MAIL_SERVER = getenv("MVC_MAIL_SERVER") ?: "192.168.10.15";
$FROM_EMAIL = getenv("MVC_FROM_EMAIL") ?: "mediatout@gmail.com";

return array(
    "DB_USER" => $DB_USER,
    "DB_PASSWORD" => $DB_PASSWORD,
    "DB_DSN" => "mysql:host=$DB_SERVER;dbname=$DB_DATABASE;charset=utf8",
    "DEBUG" => $DEBUG,
    "MAIL_SERVER" => $MAIL_SERVER,
    "FROM_EMAIL" => $FROM_EMAIL,
    "URL_VALIDATION" => $URL_VALIDATION
);

