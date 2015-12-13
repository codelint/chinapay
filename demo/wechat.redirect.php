<?php
/*
 * notice :
 *  return url must not have any url param
 *  total_fee WeChat use Fen not Yuan as unit
 */

use Omnipay\Utils\QRcode;

$config = require_once('./config.php');

$config = $config['wechat'];


/**
 * 跳转到支付界面
 * @param $config
 */
function wechat_redirect($config, $type = 'pay')
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

        $gateway->setCertPath($config['cert_path']);
        $gateway->setCertKeyPath($config['cert_key_path']);

        $out_trade_no = $_GET['out_trade_no'];
        $opts = array(
            'open_id' => array_get($config, 'open_id', false),
            'subject' => $_GET['subject'],
            'description' => '微信无效',
            'total_fee' => $_GET['total_fee'],
            'out_trade_no' => $out_trade_no,
        );
        if($type == 'prepay')
        {
            $res = $gateway->prePurchase(array_add($opts, 'trade_type', 'APP'))->send();
            echo json_encode($res->getTransactionReference());
            exit(0);
        }else{
            $res = $gateway->purchase($opts)->send();
        }

//        $res = $gateway->prePurchase($opts)->send();
//        $ret = $res->getTransactionReference();
//        echo $ret;
        $cache = get_cache();
        $cache->save($out_trade_no, $opts);

        if (!empty($_GET['redirect']))
        {
            $res->redirect('js');
        }

        if (!empty($_GET['link']))
        {
            $url = $res->getRedirectUrl();
            if (empty($_GET['qr']))
            {
                echo("<html>");
                echo("<a href=\"${url}\">$url</a>");
                echo("</html>");
            }
            else
            {
                QRcode::png($url);
            }
            die();
        }

        $res->redirect();
    } catch (\Exception $e)
    {
        var_dump($e->getMessage());
    }
}

if($_GET['pay_type'] == 'APP')
{
    wechat_redirect($config, 'prepay');
    exit();
}

$jsApi = new \Omnipay\Wechat\Sdk\JsApi();
$jsApi->init(array(
    'app_id' => $config['app_id'],
    'mch_id' => $config['mch_id'],
    'app_secret' => $config['app_secret'],
    'pay_sign_key' => $config['pay_sign_key'],
    'cert_path' => $config['cert_path'],
    'cert_key_path' => $config['cert_key_path'],
));
if (array_get($_GET, 'code', false))
{
    $jsApi->setCode($_GET['code']);
//    var_dump($_GET);
//    $url = $jsApi->createOauthUrlForOpenid();
    //Header('Location: '. $url);
    $open_id = $jsApi->getOpenid();
    $config['open_id'] = $open_id;
//    var_dump($open_id);
     wechat_redirect($config);
}
else
{
    $r_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = $jsApi->createOauthUrlForCode(urlencode($r_link));
    Header('Location: ' . $url);
}
