<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 11:33
 */

namespace Rudl\Ctrl;


use Phore\MicroApp\App;
use Rudl\Cloudfront;

class CloudfrontCtrl
{

    const ROUTE = "/v1/cloudfront/config";


    public function on_get(App $app)
    {
        try {
            $cloudfront = $app->get("cloudfront");
            return $cloudfront->getCloudFrontConfig();
        } catch (\Exception $ex) {
            return  $ret = [
                "vhosts" => [
                    [
                        "domains" => [ CONF_CLUSTER_DOMAIN ],
                        "locations" => [
                            ["location" => "/", "proxy_pass" => "http://" . CONF_PRINCIPAL_SERVICE]
                        ]
                    ]
                ]
            ];
        }

    }


}