<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    function dbConnect($host = "localhost", $port = 3306, $username = "root", $password = "abcd1234", $database = "test") {
        if(!$db = mysqli_connect($host.':'.$port, $username, $password)) {
            return FALSE;
        }
        if((strlen($database) > 0) AND (!mysqli_select_db($db, $database))) {
            return FALSE;
        }
        return $db;
    }

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // database
    // $container['db'] = function ($c){
    //     $settings = $c->get('settings')['db'];
    //     // $server = $settings['driver'].":host=".$settings['host'].";dbname=".$settings['dbname'];
    //     $server = $settings['driver'].":dbname=".$settings['dbname'].";host=".$settings['host'].";port=".$settings['port']."";
    //     $conn = new PDO($server, $settings["user"], $settings["pass"]);  
    //     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //     $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //     return $conn;
    // };

    // database
    $container['db'] = function ($c){
        $settings = $c->get('settings')['db'];
        if(!$db = mysqli_connect($settings['host'].':'.$settings['port'], $settings["user"], $settings["pass"])) {
            return FALSE;
        }
        if((strlen($settings['dbname']) > 0) AND (!mysqli_select_db($db, $settings['dbname']))) {
            return FALSE;
        }
        return $db;
    };
};
