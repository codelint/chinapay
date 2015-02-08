<?php
/*
 * notice :
 *  return url must not have any url param
 *  total_fee WeChat use Fen not Yuan as unit
 */

$config = require_once('./config.php');

$config = $config['wechat'];

/**
 * 跳转到支付界面
 * @param $config
 */
function wechat_redirect($config)
{
    /*
     * @var Omnipay\Wechat\ExpressGateway
     */
    try
    {
//        $gateway = \Omnipay\Omnipay::create('Wechat_Express');
        $gateway = new \Omnipay\Wechat\ExpressGateway();

        $gateway->setAppId($config['app_id']);
        $gateway->setKey($config['pay_sign_key']);
        $gateway->setPartner($config['partner']);
        $gateway->setPartnerKey($config['partner_key']);

        $gateway->setNotifyUrl($config['notify_url']);
        $gateway->setReturnUrl($config['return_url']);
        $gateway->setReturnUrl($config['cancel_url']);

        $out_trade_no = $_GET['out_trade_no'];
        $opts = array(
            'subject' => $_GET['subject'],
            'description' => '微信无效',
            'total_fee' => $_GET['total_fee'],
            'out_trade_no' => $out_trade_no,
        );
        $res = $gateway->purchase($opts)->send();
        $cache = get_cache();
        $cache->save($out_trade_no, $opts);

        if (!empty($_GET['redirect']))
        {
            $res->redirect('js');
        }
        $res->redirect();
    } catch (\Exception $e)
    {
        var_dump($e->getMessage());
    }
}

wechat_redirect($config);