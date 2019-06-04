<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 16:32
 */

namespace Rudl\Ctrl;


use Phore\Core\Exception\InvalidDataException;
use Phore\Core\Helper\PhoreSecretBoxSync;
use Phore\Letsencrypt\PhoreSecureCertStore;

class CloudfrontCertCtrl
{

    const ROUTE = "/v1/cloudfront/cert/:cert";

    public function on_get(string $cert, PhoreSecureCertStore $certStore)
    {
        $pem = $certStore->getCertPem($cert);
        if ($pem === null)
            throw new InvalidDataException("Cert $cert not found.");

        $encrypt = new PhoreSecretBoxSync(phore_file(CONF_CF_SECRET)->get_contents());

        echo $encrypt->encrypt($pem);

        return true;
    }

}