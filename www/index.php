<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 26.07.18
 * Time: 15:43
 */

namespace App;

use Phore\Core\Helper\PhoreSecretBoxSync;
use Phore\Letsencrypt\PhoreLetsencryptModule;
use Phore\Letsencrypt\PhoreSecureCertStore;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\VCS\VcsFactory;
use Phore\VCS\VcsRepository;
use Rudl\Cloudfront;
use Rudl\Ctrl\CloudfrontCertCtrl;
use Rudl\Ctrl\CloudfrontCtrl;
use Rudl\Ctrl\RepoPushHookCtrl;

require __DIR__ . "/../vendor/autoload.php";


$app = new App();
$app->setResponseHandler(new JsonResponseHandler());
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->acl->addRule(aclRule("*")->ALLOW());


$app->define("repo", function () : VcsRepository {
    $repo = new VcsFactory();
    $repo->setAuthSshPrivateKey(phore_file("/mnt/.ssh/id_ed25519")->get_contents());

    return $repo->repository("/mnt/repo", CONF_REPO_URL);
});


$app->define("certStore", function () : PhoreSecureCertStore {
    return new PhoreSecureCertStore(phore_file(CONF_PRINCIPAL_SECRET)->get_contents());
});


$app->define("cloudfront", function(PhoreSecureCertStore $certStore) : Cloudfront {
    return new Cloudfront($certStore);
});




$app->addModule(new PhoreLetsencryptModule());

$app->addCtrl(RepoPushHookCtrl::class);
$app->addCtrl(CloudfrontCtrl::class);
$app->addCtrl(CloudfrontCertCtrl::class);

$app->router->onGet("/", function (VcsRepository $repo) {
    return [
        "success" => true,
        "msg" => "rudl-principal ready",
        "host" => gethostname(),
        "ssh-public-key" => trim(phore_file("/mnt/.ssh/id_ed25519.pub")->get_contents()),
        "repo-url" => CONF_REPO_URL,
        "cluster-name" => CONF_CLUSTER_NAME,
        "repo-valid" => $repo->exists()
    ];
});


$app->serve();