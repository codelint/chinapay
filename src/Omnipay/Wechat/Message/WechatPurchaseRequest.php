<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * WechatPurchaseRequest:
 * @date 14-6-4
 * @time 下午2:32
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatPurchaseRequest extends BaseAbstractRequest {

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
            'productid',
            'app_key'
        );

        $params = $this->parameters->all();
        $params['appid'] = $params['app_id'];
        $params['appkey'] = $params['app_key'];
        $params['mch_id'] = $params['partner'];
        $params = array_only($params, array(
            'appid', 'productid', 'appkey',
            'noncestr', 'timestamp', 'package','mch_id'
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
        $this->response = new WechatPurchaseResponse($this, $data);
        if ($this->parameters->has('return_url'))
        {
            $this->response->setReturnUrl($this->parameters->get('return_url'));
        }
        if ($this->parameters->has('return_url'))
        {
            $this->response->setCancelUrl($this->parameters->get('cancel_url'));
        }
        if ($this->parameters->has('fail_url'))
        {
            $this->response->setFailUrl($this->parameters->get('fail_url'));
        }
        return $this->response;
    }
}
