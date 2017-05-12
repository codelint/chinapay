<?php

//if (!ini_get('display_errors'))
//{
//    ini_set('display_errors', '1');
//}

error_reporting(E_ALL);
require_once('../vendor/autoload.php');

define('CACHE_DIR', '/tmp/cache/chinapay');

define('LAST_ERROR_CACHE_KEY', 'last_error');
define('LAST_NOTIFY_CACHE_KEY', 'last_notify');

$self_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['DOCUMENT_URI'];

function base_url($uri)
{
    $self_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['DOCUMENT_URI'];
    return preg_replace('/\/[a-zA-z\.]{1,100}\.php/', $uri, $self_url);
}

function get_cache()
{
    try
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(CACHE_DIR);
        $cache->save('test.it.ok_or_not', true);
        return $cache;
    } catch (\Exception $e)
    {
        return new \Doctrine\Common\Cache\ArrayCache();
    }
}

return array(
    'alipay' => array(
        'partner' => '*',
        'key' => '*',
        'seller_email' => '*', //just need by alipay gateway
        'return_url' => base_url('/index.php'),
        'notify_url' => base_url('/alipay.notify.php'),
        'cancel_url' => base_url('/index.php'),
        'ca_cert_path' => __DIR__ . '/cer/alipay.cacert.pem'
    ),
    'wechat' => array(
        'app_id' => '*',
        'app_secret' => '*',
        'pay_sign_key' => '*',
        'partner' => '*',
        'partner_key' => '*',

        'cert_path' => '/home/dev/app/jenkins/data/workspace/www/cer/wechat/apiclient_cert.pem',
        'cert_key_path' => '/home/dev/app/jenkins/data/workspace/www/cer/wechat/apiclient_key.pem',
        'pub_cert_path' => '/home/dev/app/jenkins/data/workspace/www/cer/wechat/rootca.pem'

        'return_url' => base_url('/index.php'),
        'notify_url' => base_url('/wechat.notify.php'),
        'cancel_url' => base_url('/index.php'),
    ),

    'baifu' => array(
        'partner' => '*',
        'key' => '*',
        'return_url' => base_url('/index.php'),
        'notify_url' => base_url('/baifu.notify.php'),
        'cancel_url' => base_url('/index.php'),
    )
);
