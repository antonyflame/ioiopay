<?php

// cp -n config.php-example config.php
// vim config.php
require './config.php';
require_once '../vendor/autoload.php';

use Antonyflame\Ioiopay\Ioiopay;
use Antonyflame\Ioiopay\IoiopayException;

$ioiopay = new Ioiopay($appID, $ak, $sk, $publicKey);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['create_payment'])) {
    try {
        $qrcode = $ioiopay->createPayment(10, $_GET['out_trade_no']);
        // qrcode   weixin://wxpay/bizpayurl?pr=xxxx
        // paymentHandler('weixin://wxpay/bizpayurl?pr=xxxx');
        paymentHandler($qrcode);
    } catch (IoiopayException $e) {
        echo $e->getMessage();
        echo "<br>";
        echo "<pre>";
        var_dump($e->getAdditionalData());
        echo "</pre>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ioiopay callback
    $ioiopay->verify($_POST);
    
    // @todo update database
    $ioiopay->callbackSuccessResponse();
    exit;
}

indexHandler();

function indexHandler() {
    echo <<<HTML
<p><a href="javascript:;" class="button create_payment">点击创建支付请求，获取微信付款二维码</a></p>
HTML;
    echo javascripts();
    exit;
}

function paymentHandler($qrcode) {
    echo <<<HTML
<div>
    <p>
        <code>QRCode content: $qrcode</code>
    </p>
    <div id="qrcode"></div>
    <div>
        <p><a href="javascript:;" class="button create_payment">点击创建支付请求，获取微信付款二维码</a></p>
    </div>
</div>
HTML;
    echo javascripts();
    echo <<<HTML

<script type="text/javascript">
    new QRCode(document.getElementById("qrcode"), "$qrcode");
</script>
HTML;
    exit;
}

function javascripts() {
    // Nowdoc
    return <<<'HTML'


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
    function generateTradeNo(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    $("a.create_payment").on("click", function(event) {
        if ($(this).hasClass('clicked')) { 
            event.preventDefault();
            return false;
        } else {
            $(this).addClass('clicked');
            window.location.href = '?create_payment=1&out_trade_no=' + generateTradeNo(16);
        }
    });
</script>
<script type="text/javascript" src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
HTML;
}

