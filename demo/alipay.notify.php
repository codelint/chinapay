<?php
$config = require_once('./config.php');


$config = $config['alipay'];

/**
 * 支付成功通知函数, POST REQUEST FROM Alipay Server
 * @param $config
 * @param $get
 * @param $post
 */
function notify_callback($config, $get, $post)
{
    $cache = get_cache();
    try
    {
        $gateway = \Omnipay\Omnipay::create('Alipay_WapExpress');
        $gateway->setPartner($config['partner']);
        $gateway->setKey($config['key']);
        $gateway->setSellerEmail($config['seller_email']);

        $params = array_merge($get, $post);

        $options['ca_cert_path'] = $config['ca_cert_path'];
        $options['sign_type'] = 'MD5';
        $options['request_params'] = $params;

        $cache->save(LAST_NOTIFY_CACHE_KEY, array(
            'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'get' => http_build_query($get), 'post' => http_build_query($post)
        ));

        $response = $gateway->completePurchase($options)->send();
        if ($response->isSuccessful() && $response->isTradeStatusOk())
        {
            //todo success
            // if u use the wap alipay u need to parse the trade info from .notify_data
            $params = \Omnipay\Utils\Utils::format_wap_alipay_notify($params);
            $no = $params['out_trade_no'];
            $opts = $cache->fetch($no);
            $opts['notify'] = array('_GET' => $get, '_POST' => $post);
            $opts['status'] = '支付成功';

            $cache->delete(LAST_ERROR_CACHE_KEY);
            $cache->save(LAST_NOTIFY_CACHE_KEY, array(
                'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'get' => http_build_query($get), 'post' => http_build_query($post),
                'out_trade_no' => $no,
                'status' => 'success'
            ));

            $cache->save($no, $opts);

            // success return 'success' to response to tell alipay's server, to stop continuing notify request
//            die('continue') //return continue just for debug;
            die('success');
        }
        else
        {
            $cache->save(LAST_ERROR_CACHE_KEY, $options);
            die('fail');
        }
    } catch (\Exception $e)
    {
        $cache->save(LAST_ERROR_CACHE_KEY, $e->getLine() . ': ' . $e->getMessage());
        die('exception');
    }
}

notify_callback($config, $_GET, $_POST);