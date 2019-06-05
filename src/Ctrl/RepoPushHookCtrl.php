<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 15:56
 */

namespace Rudl\Ctrl;


use Phore\VCS\VcsRepository;

class RepoPushHookCtrl
{

    const ROUTE = "/v1/hooks/repo";


    public function on_get(VcsRepository $repo)
    {

        ignore_user_abort(true);
        $repo->pull();
        return ["success"=> true];

    }

}