<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * WechatPrePurchaseRequest:
 * @date 15/2/12
 * @time 00:50
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatPrePurchaseRequest extends BaseAbstractRequest {

    protected $endpoint = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    public function initialize(array $parameters = array())
    {
        if (null !== $this->response)
        {
            throw new \RuntimeException('Request cannot be modified after it has been sent!');
        }

        $this->parameters = new ParameterBag;
        foreach ($parameters as $k => $v)
        {
            $this->parameters->set($k, $v);
        }
        return $this;
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate(
            'app_id',
            'app_key',
            'partner',
            'body',
            'out_trade_no',
            'total_fee',
            'spbill_create_ip',
            'notify_url'
        );

        $params = array_only($this->parameters->all(), array(
            'app_id', 'app_key', 'partner',
            'body', 'out_trade_no', 'total_fee',
            'spbill_create_ip', 'notify_url', 'trade_type',
            'open_id', 'product_id',
            'device_info', 'attach',
            'time_start', 'time_expire', 'goods_tag',
        ));
        $params['appid'] = $params['app_id'];
        if (!empty($params['open_id']))
        {
            $params['openid'] = $params['open_id'];
        }
        $params['mch_id'] = $params['partner'];
        $params['nonstr'] = str_random(8);
        $params['time_start'] = date('YmdHis');
        $params['trade_type'] = array_get($params, 'trade_type', 'JSAPI');
        return array_except($params, ['app_id', 'open_id', 'partner']);
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $params = array_except($data, 'app_key');
        ksort($params);
        $qstr = http_build_query($data);
        $qstr = $qstr . '&key=' . $data['app_key'];
        $sign = md5($qstr);

        $xml_str = '<xml>';
        foreach($params as $k => $v)
        {
            $xml_str = $xml_str . "<$k>" . $v . "</$k>";
        }
        $xml_str = $xml_str . "<sign>$sign</sign></xml>";

        $ret = $this->postXml($xml_str, $this->endpoint, 'todo');
        $arr = xmlToArray(simplexml_load_string($ret));
        $this->response = new WechatPrePurchaseResponse($this, $arr);
        return $this->response;
    }

}