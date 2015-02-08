<?php namespace Omnipay\Netpay;
use Omnipay\Common\AbstractGateway;

/**
 * NetpayGateway:
 * @date 14-3-18
 * @time 下午2:48
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
        return 'Netpay_Express';
    }

    function setKey($filepath)
    {
        $this->setParameter('key_file', $filepath);
    }

    function getKey()
    {
        return $this->getParameter('key_file');
    }

    function setCert($filepath)
    {
        $this->setParameter('cert_file', $filepath);
    }

    function getCert()
    {
        $this->getParameter('cert_file');
    }

    function getPartner()
    {
        return $this->getParameter('partner');
    }

    function setPartner($id)
    {
        $this->setParameter('partner', $id);
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
        return [

        ];
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Netpay\Message\NetpayPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Netpay\Message\NetpayCompletePurchaseRequest', $parameters);
    }
}
