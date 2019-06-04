<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 17:00
 */


namespace App;

use Phore\Letsencrypt\PhoreLetsencrypt;
use Phore\Letsencrypt\PhoreSecureCertStore;
use Rudl\Cloudfront;

require __DIR__ . "/../vendor/autoload.php";



$certStore = new PhoreSecureCertStore(phore_file(CONF_PRINCIPAL_SECRET)->get_contents());
$letsencrypt = new PhoreLetsencrypt("m@tth.es");


$cloudFront = new Cloudfront($certStore);

foreach ($cloudFront->getCertIdToDomainMap() as $certId => $domains) {
    phore_out("Checking ssl_cert_id '$certId' for domains '" . implode(", ", $domains) . "'");
    try {
        $certStore->acquireCertIfNeeded($certId, $domains, $letsencrypt);
    } catch (\Exception $e) {
        phore_out("Error: " . $e->getMessage());
    }
}

phore_out("Done checking certs.");