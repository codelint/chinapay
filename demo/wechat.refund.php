<?php

$config = require_once('./config.php');

$config = $config['wechat'];

$data = [
    'out_trade_no' => $_GET['out_trade_no'],
    'transaction_id' => $_GET['transaction_id'],
    'out_refund_no' => $_GET['out_refund_no'],
    'total_fee' => $_GET['total_fee'],
    'refund_fee' => $_GET['refund_fee']
];

$data = array_filter($data);

$gateway = new \Omnipay\Wechat\ExpressGateway();


$gateway->setAppId($config['app_id']);
$gateway->setKey($config['pay_sign_key']);
$gateway->setPartner($config['partner']);
$gateway->setPartnerKey($config['partner_key']);

$gateway->setCertPath($config['cert_path']);
$gateway->setCertKeyPath($config['cert_key_path']);
$gateway->setPubCertPath($config['pub_cert_path']);


$req = $gateway->refund($data);
$res = $req->send();

echo json_encode($res->getTransactionReference());