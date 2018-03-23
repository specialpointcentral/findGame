<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/3/14
 * Time: 15:12
 * this function is used to make QR code
 */
/**
 * @param $passkey passkey where use $baseURL."?key=".passkey
 */
function QR_respose($baseURL,$passkey){

}

function QR_base64($baseURL,$passkey){

    return base64_encode(QR_respose($baseURL,$passkey));
}
?>