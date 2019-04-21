<?php namespace Antonyflame\Ioiopay;

use InvalidArgumentException;
use Exception;

// docs link: https://ioiopay.com/ioiopay-doc.html
class Ioiopay
{
    protected $client;

    protected $appID;
    protected $ak;
    protected $sk;
    protected $publicKey;

    public function __construct($appID, $ak, $sk, $publicKey) {
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'https://hehehub.com']);
        $this->appID = $appID;
        $this->ak = $ak;
        $this->sk = $sk;
        $this->publicKey = $publicKey;
    }

    public function createPayment($amount, $outTradeNo)
    {
        if ($amount < 10) {
            throw new InvalidArgumentException('amount must be greater than or equal to 10');
        }
        if ($amount > 300) {
            throw new InvalidArgumentException('amount must be less than or equal to 300');
        }

        $signData = [
            "ak" => $this->ak,
            "appID" => $this->appID,
            "amount" => $amount,
            "outTradeNo" => $outTradeNo,
        ];

        $sign = $this->md5Sign($this->sk, $signData);
        $signData['sign'] = $sign;
        $encrypted = $this->rsaEncrypt(json_encode($signData));

        $res = $this->client->request('POST', '/api/order/',
            [
                'form_params' => [
                    'ak' => $this->ak,
                    'encryptData' => $encrypted,
                ],
            ]);

        if ($res->getStatusCode() !== 200) {
            throw new IoiopayException('ioiopay status code is not 200');
        }

        $resBody = json_decode($res->getBody(), true);
        if ($resBody['code'] == -1) {
            throw new IoiopayException('ioiopay request error', $resBody);
        }

        return $resBody['data']['payUrl'];
    }

    public function verify($callbackData) {
        $sign = $callbackData['sign'];
        unset($callbackData['sign']);
        $this->checkSign($callbackData, $sign, $this->publicKey);
    }

    public function callbackSuccessResponse() {
        echo 'success';
    }

    protected function checkSign($data, $sign, $publicKey)
    {
        $dataStr = $this->getSignString($data);
        $ok = openssl_verify($dataStr, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256);

        if ($ok === -1) {
            $errString = openssl_error_string() ?? '';
            throw new Exception('openssl_verify error: ' . $errString);
        }

        if ($ok === 0) {
            throw new IoiopayException('Signature is incorrect');
        }
    }

    protected function md5Sign(string $sk, array $data): string
    {
        $dataStr = $this->getSignString($data);
        $dataStr .= $sk;
        return md5($dataStr);
    }

    protected function rsaEncrypt(string $data): string
    {
        $ok = openssl_public_encrypt($data, $encrypted, $this->publicKey);
        if (!$ok) {
            throw new Exception('encrypt error: ' . openssl_error_string());
        }
        return base64_encode($encrypted);
    }

    protected function getSignString($data) {
        ksort($data);
        reset($data);
        $pairs = [];
        foreach ($data as $key => $val) {
            $pairs[] = "{$key}={$val}";
        }
        return implode("&", $pairs);
    }
}
