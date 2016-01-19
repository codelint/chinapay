<?php namespace Omnipay\Baifu;

use Omnipay\Common\AbstractGateway;

/**
 * ExpressGateway:
 * @date 16/1/18
 * @time 18:07
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressGateway extends AbstractGateway {

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'Baifu_Express';
    }

    function setKey($filepath)
    {
        $this->setParameter('key', $filepath);
    }

    function getKey()
    {
        return $this->getParameter('key');
    }

    function getPartner()
    {
        return $this->getParameter('partner_id');
    }

    function setPartner($id)
    {
        $this->setParameter('partner_id', $id);
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

    function getReturnUrl()
    {
        return $this->getParameter('return_url');
    }

    function setCertPath($cert)
    {
        $this->setParameter('cert_path', $cert);
    }

    function getCertPath()
    {
        $this->getParameter('cert_path');
    }


    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Baifu\Message\ExpressPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Baifu\Message\ExpressCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Baifu\Message\ExpressCompletePurchaseRequest', $parameters);
    }
}