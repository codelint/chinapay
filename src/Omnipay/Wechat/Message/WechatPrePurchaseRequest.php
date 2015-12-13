<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Wechat\Sdk\UnifiedOrder;
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
            'notify_url',
            'cert_path',
            'cert_key_path'
        );

        $params = array_only($this->parameters->all(), array(
            'app_id', 'app_key', 'partner',
            'body', 'out_trade_no', 'total_fee',
            'spbill_create_ip', 'notify_url', 'trade_type',
            'open_id', 'product_id',
            'device_info', 'attach',
            'time_start', 'time_expire', 'goods_tag', 'cert_path', 'cert_key_path'
        ));
        $params['appid'] = $params['app_id'];
        $params['openid'] = $params['open_id'];
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
        $unifiedOrder = new UnifiedOrder();
        $unifiedOrder->init(array(
            'app_id' => $data['appid'],
            'mch_id' => $data['mch_id'],
            'app_secret' => '',
            'pay_sign_key' => $data['app_key'],
            'cert_path' => $data['cert_path'],
            'cert_key_path' => $data['cert_key_path'],
        ));
        //sign已填,商户无需重复填写

        $unifiedOrder->setParameter('body', $data['body']); //商品描述
        $unifiedOrder->setParameter('out_trade_no', $data['out_trade_no']); //商户订单号
        $unifiedOrder->setParameter('total_fee', $data['total_fee']); //总金额
        $unifiedOrder->setParameter('notify_url', $data['notify_url']); //通知地址
        $unifiedOrder->setParameter('trade_type', $data['trade_type']); //交易类型
        if($data['trade_type'] == 'JSAPI')
        {
            $unifiedOrder->setParameter('openid', $data['openid']);
        }

        $ret = $unifiedOrder->getResult();
        $this->response = new WechatPrePurchaseResponse($this, $ret);
        return $this->response;
    }

}