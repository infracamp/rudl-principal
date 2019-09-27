<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 04.06.19
 * Time: 15:56
 */

namespace Rudl\Ctrl;


use Phore\Core\Helper\PhoreSecretBoxSync;
use Phore\MicroApp\Type\Request;
use Phore\VCS\VcsRepository;
use Rudl\Config;
use Rudl\DeployManager;

class RepoPushHookCtrl
{

    const ROUTE = "/v1/hooks/repo";


    private function mapUpdateStacksToStacks (string $updateStack = null, Config $config) : ?array
    {
        if ($updateStack === null)
            return null;
        $configData = $config->getConfigFile();

        $stacks = phore_pluck(["stack_map", $updateStack], $configData, null);
        if ($stacks === null)
            return [$updateStack];

        if ( ! is_array($stacks))
            $stacks = [ $stacks ];
        if (count ($stacks) === 0)
            return null;
        return $stacks;
    }


    public function on_get(Request $request, VcsRepository $repo, DeployManager $deployManager, PhoreSecretBoxSync $secretBox, Config $config)
    {
        // Todo: check auth token
        $token = $request->GET->get("token", null);

        $updateName = $request->GET->get("name", null);

        ignore_user_abort(true);

        $repo->pull();
        $deployManager->registerAuth($secretBox);

        $stacksToUpdate = $this->mapUpdateStacksToStacks($updateName, $config);
        $log = $deployManager->deployStacks($stacksToUpdate);

        return ["success"=> true, "log" => $log];
    }

}