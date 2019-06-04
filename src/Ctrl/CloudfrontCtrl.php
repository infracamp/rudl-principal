<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 11:33
 */

namespace Rudl\Ctrl;


use Rudl\Cloudfront;

class CloudfrontCtrl
{

    const ROUTE = "/v1/cloudfront/config";


    public function on_get(Cloudfront $cloudfront)
    {

        return $cloudfront->getCloudFrontConfig();

    }


}