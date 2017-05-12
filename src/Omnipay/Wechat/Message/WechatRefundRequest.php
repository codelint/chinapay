<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Wechat\Sdk\Refund;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * WechatRefundRequest:
 * @date 2017/5/11
 * @time 15:37
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatRefundRequest extends BaseAbstractRequest {

    protected $endpoint = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    const REFUND_SOURCE_UNSETTLED_FUNDS = 'REFUND_SOURCE_UNSETTLED_FUNDS';
    const REFUND_SOURCE_RECHARGE_FUNDS = 'REFUND_SOURCE_RECHARGE_FUNDS';

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
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @return mixed
     */
    public function getData()
    {
        $this->validate(
            'app_id',
            'partner',
            'app_key',
            'out_refund_no',
            'total_fee',
            'refund_fee',
            'cert_path',
            'cert_key_path',
            'pub_cert_path'
        );
        $params = $this->parameters->all();

        if (empty($params['transaction_id']) && empty($params['out_trade_no']))
        {
            throw new InvalidRequestException("The transaction_id or out_trade_no parameter is required");
        }
        $params['appid'] = $params['app_id'];
        $params['appkey'] = $params['app_key'];
        $params['mch_id'] = $params['partner'];
        $params['op_user_id'] = array_get($params, 'op_user_id', $params['partner']);
        $params['refund_account'] = array_get($params, 'refund_account', static::REFUND_SOURCE_RECHARGE_FUNDS);
        $params = array_only($params, array(
            'appid', 'appkey',
            'refund_fee', 'total_fee', 'out_trade_no',
            'transaction_id', 'op_user_id', 'out_refund_no',
            'noncestr', 'timestamp', 'mch_id', 'cert_path', 'cert_key_path', 'pub_cert_path', 'refund_account'
        ));
        return $params;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $refund = new Refund();
        $refund->init(array(
            'app_id' => $data['appid'],
            'mch_id' => $data['mch_id'],
            'app_secret' => '',
            'pay_sign_key' => $data['appkey'],
            'cert_path' => $data['cert_path'],
            'cert_key_path' => $data['cert_key_path'],
            'pub_cert_path' => $data['pub_cert_path']
        ));
        //sign已填,商户无需重复填写
        if (!empty($data['out_trade_no']))
        {
            $refund->setParameter('out_trade_no', $data['out_trade_no']); //商户订单号
        }
        if (!empty($data['transaction_id']))
        {
            $refund->setParameter('transaction_id', $data['transaction_id']); //商户订单号
        }
        $refund->setParameter('total_fee', $data['total_fee']); //总金额
        $refund->setParameter('refund_fee', $data['refund_fee']); //退款金额
        $refund->setParameter('out_refund_no', $data['out_refund_no']); //退款单号
        $refund->setParameter('op_user_id', $data['op_user_id']); //交易类型
        $refund->setParameter('refund_account', $data['refund_account']);

        $ret = $refund->getResult();
        $this->response = new WechatRefundResponse($this, $ret);
        return $this->response;
    }
}