<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 17:41
 */

namespace App;

require __DIR__ . "/../vendor/autoload.php";


while (true) {
    try {
        phore_http_request("http://localhost/v1/hooks/repo")->send()->getBodyJson();
        phore_out("Pull of repository successful. Starting normal operations.");
        break;
    } catch (\Exception $e) {
        phore_out("Pull of repository failed. Retrying in 30 seconds. (Msg: " . $e->getMessage(). ")");
    }
    sleep(30);
}