<?php namespace Omnipay\Baifu\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * ExpressPurchaseRequest:
 * @date 16/1/18
 * @time 22:48
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressPurchaseRequest extends AbstractRequest {

    protected function getParameter($key, $default = '')
    {
        return $this->parameters->get($key, $default);
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
            'key',
            'partner_id',
            'out_trade_no',
            'subject',
            'total_fee'
        );
        // $character_set = $this->getParameter('character_set', 'GBK');
        $sp_pass_through = $this->getParameter('sp_pass_through', false);
        $bank_no = $this->getParameter('bank_no', false);
        $params = [
            'service_code' => 1,
            'sp_no' => $this->getParameter('partner_id'),
            'order_create_time' => date('YmdHis'),
            'order_no' => $this->getParameter('out_trade_no'),
            'goods_name' => $this->getParameter('subject'),
            'goods_desc' => $this->getParameter('body', ''),
            'total_amount' => $this->getParameter('total_fee')*100,
            'currency' => 1,
            'return_url' => $this->getParameter('notify_url'),
            'page_url' => $this->getParameter('return_url'),
            'pay_type' => $this->getParameter('type', 1),
            'sign_method' => 1,
            'input_charset' => 1,
            'extra' => $this->getParameter('extra', ''),
            'version' => 2
        ];
        if($sp_pass_through)
        {
            $params['sp_pass_through'] = $sp_pass_through;
        }
        if($bank_no)
        {
            $params['bank_no'] = $bank_no;
        }
        ksort($params);
        $sign_str = '';
        foreach($params as $k => $v)
        {
            $sign_str .= "$k=$v&";
        }
        $sign_str = $sign_str . 'key=' . $this->getParameter('key');
        $params['sign'] = md5($sign_str);
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
        $this->response = new ExpressPurchaseResponse($this, $data);
        return $this->response;
    }
}