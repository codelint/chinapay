<?php namespace Omnipay\Netpay\Message;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * NetpayResponse: 
 * @date 14-3-18
 * @time 下午4:13
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class NetpayPurchaseResponse extends AbstractResponse implements RedirectResponseInterface{

    public function isRedirect(){
        return true;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndPoint();
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return $this->getData();
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return false;
    }
}
