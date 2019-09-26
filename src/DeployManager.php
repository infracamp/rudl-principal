<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.09.19
 * Time: 17:40
 */

namespace Rudl;


use Phore\Core\Exception\InvalidDataException;
use Phore\Core\Helper\PhoreSecretBoxSync;

class DeployManager
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DockerMgr
     */
    private $dockerMgr;

    /**
     * @var StackStatus
     */
    private $stackStatus;

    public function __construct(Config $config, DockerMgr $dockerMgr, StackStatus $stackStatus)
    {
        $this->config = $config;
        $this->dockerMgr = $dockerMgr;
        $this->stackStatus = $stackStatus;
    }


    public function deployAllStacks ()
    {
        foreach ($this->config->getStacks() as $stackName => $stackFile) {
            try {
                $this->dockerMgr->stackDeploy($stackName, $stackFile);
                $this->stackStatus->set($stackName, "OK (" . date ("Y-m-d H:i:s") . ")");
            } catch (\Exception $e) {
                $this->stackStatus->set($stackName, [
                    "last_update" => date("Y-m-d H:i:s"),
                    "error" => $e->getMessage()
                ]);
            }
        }
    }


    /**
     * Login to specified registrys
     *
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * !! Security relevant method. Change only if you know what you are doing !!
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     *
     *
     * @throws \Exception
     */
    public function registerAuth(PhoreSecretBoxSync $secretBoxSync)
    {
        $config = $this->config->getConfigFile();


        foreach ((phore_pluck("registry_auth", $config, []) ?? []) as $registry => $rAuth) {
            try {
                $this->dockerMgr->dockerLogin(
                    phore_pluck("user", $rAuth, new InvalidDataException("User field missing in registry_auth.$registry")),
                    $secretBoxSync->decrypt(phore_pluck("enc-pass", $rAuth, new InvalidDataException("pass field missing in registry_auth.$registry"))),
                    $registry
                );
            } catch (\Exception $ex) {
                // Leave this exception here to prevent password leaking from stack traces.
                throw new \Exception("Cannot login to registry '$registry': Username/password incorrect: " . $ex->getMessage());
            }
        }
    }

}