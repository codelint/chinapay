<?php
$config = require_once('./config.php');

$config = $config['baifu'];

/**
 * 支付宝: 跳转到支付界面
 * @param $config
 */
function alipay_redirect($config)
{
    /*
     * @var Omnipay\Alipay\WapExpressGateway
     */
    try
    {
        // $gateway = \Omnipay\Omnipay::create('Alipay_WapExpress')
        $gateway = new \Omnipay\Baifu\ExpressGateway();
        $gateway->setPartner($config['partner']);
        $gateway->setKey($config['key']);
        $gateway->setNotifyUrl($config['notify_url']);
        $gateway->setReturnUrl($config['return_url']);
        $gateway->setCertPath($config['cert_path']);
        $opts = array(
            'sp_pass_through' => '%7B%22offline_pay%22%3A1%7D',
            'subject' => $_GET['subject'],
            'description' => '暂无',
            'total_fee' => $_GET['total_fee'],
            'out_trade_no' => $_GET['out_trade_no'],
        );
        $res = $gateway->purchase($opts)->send();
//        $res = $gateway->completePurchase($opts)->send();
//        $abc = [ 'result' => $res->getTransactionReference(), 'trade_status' => $res->isTradeStatusOk()];
//        var_dump($abc);
        $cache = get_cache();
        $cache->save($_GET['out_trade_no'], $opts);
        $res->redirect();
//        header("Content-type: application/json");
//        echo json_encode([
//            'url' => $res->getRedirectUrl(),
//            'opts' => $opts,
//            'config' => $config,
//            'info_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/demo/info.php?out_trade_no=' . $out_trade_no,
//        ]);
    } catch (\Exception $e)
    {
        var_dump($e->getMessage());
    }
}

alipay_redirect($config);