<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 11:41
 */

namespace Rudl;


use Phore\FileSystem\PhoreFile;
use Phore\FileSystem\PhoreUri;
use Phore\Letsencrypt\PhoreSecureCertStore;

class Cloudfront
{

    /**
     * @var \Phore\FileSystem\PhoreDirectory
     */
    private $confPath;

    private $secureCertStore;

    public function __construct(PhoreSecureCertStore $phoreSecureCertStore, $configDir = "/mnt/repo/" . CONF_CLUSTER_NAME . "/cloudfront.d/")
    {
        $this->confPath = phore_dir($configDir)->assertDirectory();
        $this->secureCertStore = $phoreSecureCertStore;
    }

    public function getCloudFrontConfig ()
    {
        $ret = [
            "vhosts" => []
        ];

        $this->confPath->walkR(function (PhoreUri $file) use (&$ret) {
            if ( ! $file instanceof PhoreFile)
                return true;
            if ( ! in_array($file->getExtension(), ["yml", "yaml"]))
                return true;

            $data = $file->get_yaml();

            $ssl_cert_id = phore_pluck("ssl_cert_id", $data, null);
            if ($ssl_cert_id !== null) {
                $meta = $this->secureCertStore->getCertMeta($ssl_cert_id);
                if ($meta !== null) {
                    $data["ssl_cert_serial"] = $meta->cert_serialNumber;
                } else {
                    unset ($data["ssl_cert_id"]);
                }

            }

            $ret["vhosts"][] = $data;
        });
        return $ret;
    }


    public function getCertIdToDomainMap()
    {
        $ret = [];

        $this->confPath->walkR(function (PhoreUri $file) use (&$ret) {
            if ( ! $file instanceof PhoreFile)
                return true;
            if ( ! in_array($file->getExtension(), ["yml", "yaml"]))
                return true;

            $data = $file->get_yaml();

            $ssl_cert_id = phore_pluck("ssl_cert_id", $data, null);
            if ($ssl_cert_id === null)
                return true;

            $domains = phore_pluck("domains", $data, []);

            if ( ! isset ($ret[$ssl_cert_id]))
                $ret[$ssl_cert_id] = [];

            foreach ($domains as $domain)
                $ret[$ssl_cert_id][] = strtolower($domain);

        });
        return $ret;
    }



}