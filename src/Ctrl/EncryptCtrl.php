<?php


namespace Rudl\Ctrl;




use Phore\Core\Helper\PhoreSecretBoxSync;
use Phore\MicroApp\Type\Request;

class EncryptCtrl
{

    const ROUTE = "/v1/encrypt";

    public function on_get() {

        echo "<form method='POST' action=''><input type='text' name='value'></form>";
        return true;

    }

    public function on_post(Request $request, PhoreSecretBoxSync $secretBox) {

        $data = $request->POST->get("value");

        echo "<pre>" . $secretBox->encrypt($data) . "</pre>";
        return true;

    }

}