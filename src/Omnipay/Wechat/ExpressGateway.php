<?php namespace Omnipay\Wechat;

use Omnipay\Common\AbstractGateway;

/**
 * WeChat:
 * @date 14-6-4
 * @time 下午2:27
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressGateway extends AbstractGateway {

    const delivery_entry = 'https://api.weixin.qq.com/pay/delivernotify';

    const DELIVERY_SUCCESS = 1;
    const DELIVERY_FAIL = 0;

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'Wechat_Express';
    }

    function setAppId($appId)
    {
        $this->setParameter('app_id', $appId);
    }

    function getAppId()
    {
        return $this->getParameter('app_id');
    }

    function setKey($key)
    {
        $this->setParameter('app_key', $key);
    }

    function getKey()
    {
        return $this->getParameter('app_key');
    }

    function getPartner()
    {
        return $this->getParameter('partner');
    }

    function setPartner($id)
    {
        $this->setParameter('partner', $id);
    }

    function getPartnerKey()
    {
        return $this->getParameter('partner_key');
    }

    function setPartnerKey($key)
    {
        $this->setParameter('partner_key', $key);
    }

    function setCertPath($path){
        $this->setParameter('cert_path', $path);
    }

    function getCertPath()
    {
        $this->getParameter('cert_path');
    }

    function setCertKeyPath($path)
    {
        $this->setParameter('cert_key_path', $path);
    }

    function getCertKeyPath()
    {
        return $this->getParameter('cert_key_path');
    }

    function setPubCertPath($path){
        $this->setParameter('pub_cert_path', $path);
    }

    function getPubCertPath(){
        return $this->getParameter('pub_cert_path');
    }

    function getNotifyUrl()
    {
        return $this->getParameter('notify_url');
    }

    function setNotifyUrl($url)
    {
        $this->setParameter('notify_url', $url);
    }

    function setReturnUrl($url)
    {
        $this->setParameter('return_url', $url);
    }

    function getReturnUrl($url)
    {
        return $this->getParameter('return_url');
    }

    function setCancelUrl($url)
    {
        $this->setParameter('cancel_url', $url);
    }

    function getCancelUrl($url)
    {
        return $this->getParameter('cancel_url', $url);
    }

    function setFailUrl($url)
    {
        $this->setParameter('cancel_url', $url);
    }

    function getFailUrl($url)
    {
        return $this->getParameter('fail_url', $url);
    }


    /**
     * Define gateway parameters, in the following format:
     *
     * array(
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => array('billing', 'login'), // enum variable, first item is default
     * );
     */
    public function getDefaultParameters()
    {
        return array(
            'timestamp' => time(),
            'noncestr' => str_random(16),
        );
    }

    /**
     * 微信JS支付回调xml转换format成array
     * @param $xml_str
     * @return array
     */
    static function xml2array_by_wechat_notify_body($xml_str)
    {
        $postObj = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        return array(
            'AppId' => (string)$postObj->AppId,
            'TimeStamp' => (string)$postObj->TimeStamp,
            'NonceStr' => (string)$postObj->NonceStr,
            'OpenId' => (string)$postObj->OpenId,
            'IsSubscribe' => (string)$postObj->IsSubscribe,
            'AppSignature' => (string)$postObj->AppSignature
        );
    }

    /**
     * create the package string
     * @param array $params ['productid' => '']
     * @return string
     */
    public function createPackageStr($params)
    {
        $out_trade_no = $params['productid'];
        $fee = $params['money_paid'];
        $opts = array(
            'bank_type' => 'WX',
            'body' => $params['subject'],
            'partner' => $this->getPartner(),
            'out_trade_no' => $out_trade_no,
            'total_fee' => abs($fee) * 100,
            'fee_type' => 1,
            'notify_url' => isset($params['notify_url']) ? $params['notify_url'] : $this->getNotifyUrl(),
            'spbill_create_ip' => '127.0.0.1',
            'input_charset' => 'UTF-8'
        );
        ksort($opts);
        $qstr = http_build_query($opts);
        $sign = strtoupper(md5(urldecode($qstr) . '&key=' . $this->getPartnerKey()));

        return $qstr . '&sign=' . $sign;
    }

    /**
     * 支付
     * @param array $parameters [ 'out_trade_no' => '', 'description' => '', 'subject' => ''
     * @throws \RuntimeException
     * @return mixed
     */
    public function purchase(array $parameters = array())
    {
        if (empty($parameters['prepay_id']))
        {
            if(!isset($parameters['open_id'])){
                throw new \RuntimeException('lack open id');
            }
            $res = $this->prePurchase($parameters)->send();
            $ref = $res->getTransactionReference();
            if (empty($ref['prepay_id']))
            {
                throw new \RuntimeException('get prepay_id failed');
            }
            else
            {
                $parameters['prepay_id'] = $ref['prepay_id'];
            }
        }
        //获取open_id
        $parameters['package'] = 'prepay_id=' . $parameters['prepay_id'];
        $parameters = array_key_map($parameters, ['out_trade_no' => 'productid']);
        $params = array_only($parameters, ['appid', 'timestamp', 'noncestr', 'productid', 'package', 'open_id']);
        return $this->createRequest('\Omnipay\Wechat\Message\WechatPurchaseRequest', $params);
    }

    /**
     * 获取预支付号
     * @param array $parameters ['subject', 'open_id', 'total_fee', 'out_trade_no']
     * @return mixed
     */
    function prePurchase(array $parameters = array())
    {
        $parameters = array_key_map($parameters, ['subject' => 'body']);

        $params = array_only($parameters, ['out_trade_no', 'total_fee', 'body', 'open_id', 'trade_type']);
        $params['total_fee'] = abs($params['total_fee']) * 100;
        $params['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        // $params['trade_type'] =array_get($pa) 'JSAPI';

        return $this->createRequest('\Omnipay\Wechat\Message\WechatPrePurchaseRequest', $params);
    }

    /**
     * 完成支付
     * @param array $parameters ['params' => [], 'body' => '']
     * @return \Omnipay\Wechat\Message\WechatCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        // $parameters['body'] = static::xml2array_by_wechat_notify_body($parameters['body']);
        return $this->createRequest('\Omnipay\Wechat\Message\WechatCompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $params
     * @return \Omnipay\Wechat\Message\WechatRefundRequest
     */
    public function refund(array $params = array()){
        $params['total_fee'] = abs($params['total_fee']) * 100;
        $params['refund_fee'] = abs($params['refund_fee']) * 100;
        return $this->createRequest('\Omnipay\Wechat\Message\WechatRefundRequest', $params);
    }

    /**
     * 微信发货通知
     * entry: https://api.weixin.qq.com/pay/delivernotify?access_token=xxxxxx
     * post data:
     *  {
     *      "appid" : "wwwwb4f85f3a797777",
     *      "openid" : "oX99MDgNcgwnz3zFN3DNmo8uwa-w",
     *      "transid" : "111112222233333",
     *      "out_trade_no" : "555666uuu",
     *      "deliver_timestamp" : "1369745073",
     *      "deliver_status" : "1",
     *      "deliver_msg" : "ok",
     *      "app_signature" : "53cca9d47b883bd4a5c85a9300df3da0cb48565c", "sign_method" : "sha1"
     *  }
     */
    public function deliveryNotify($pack, $access_token)
    {
        $pack = array_only($pack, ['appid', 'openid', 'transid', 'out_trade_no',
            'deliver_timestamp', 'deliver_status', 'deliver_msg']);
        $pack['appid'] = $this->getAppId();
        $uri = static::delivery_entry . '?access_token=' . $access_token;
        $pack['app_signature'] = sha1(ksort(array_add($pack, 'appkey', $this->getKey())));
        $pack['sign_method'] = 'sha1';
        //todo
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, 1);

//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//        curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, json_encode($pack));

        $res = curl_exec($ch);
        if (curl_errno($ch) <> 0)
        {
//            $error_msg = "Error Number:-00006, Error Description: GET_RESULT_ERROR（获取结果失败:" . curl_errno($ch);
            curl_close($ch);
            return false;
        }
        else
        {
            curl_close($ch);
            if (strlen($res) == 0)
            {
//                $error_msg = "Error Number:-00001, Error Description: RETURN_BLANK（远程服务器返回空页面）";
                return false;
            }
            else
            {
                return $res;
            }
        }
    }
}
