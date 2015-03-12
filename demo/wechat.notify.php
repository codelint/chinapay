<?php
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

        $out_trade_no = time() . rand(1000, 9999);
        $opts = array(
            'subject' => "你正在为订单[$out_trade_no]支付...",
            'description' => '微信无效',
            'total_fee' => 0.01,
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

function notify_callback($config, $get, $raw_post)
{
    $cache = get_cache();
    try
    {
        $gateway = new \Omnipay\Wechat\ExpressGateway();

        $gateway->setAppId($config['app_id']);
        $gateway->setKey($config['pay_sign_key']);
        $gateway->setPartner($config['partner']);
        $gateway->setPartnerKey($config['partner_key']);

        $cache->save(LAST_NOTIFY_CACHE_KEY, func_get_args());
        $response = $gateway->completePurchase(array('request_params' => $get, 'body' => $raw_post))->send();
        if ($response->isSuccessful() && $response->isTradeStatusOk())
        {
            //todo success

            $serial = $cache->fetch($get['out_trade_no']);
            $serial['notify'] = array('status' => 'success', 'param' => http_build_query($get), 'body' => $raw_post);
            $data = json_decode(json_encode(simplexml_load_string($raw_post, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            $cache->save($get['out_trade_no'], $serial);

            $cache->save(LAST_NOTIFY_CACHE_KEY, array(
                'param' => http_build_query($get), 'body' => $raw_post,
                'data' => $data,
                'status' => $response->getMessage()));
            $cache->delete(LAST_ERROR_CACHE_KEY);

            die($response->getMessage());
//            die('success');
        }
        else
        {
            die($response->getMessage());
        }
    } catch (\Exception $e)
    {
        $cache->save(LAST_ERROR_CACHE_KEY, $e->getLine() . ': ' . $e->getMessage());
        die('exception: '. $e->getLine() . ' - ' . $e->getMessage());
    }
}

notify_callback($config, $_GET, empty($_POST) ? file_get_contents('php://input') : $_POST);