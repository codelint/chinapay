<?php namespace Omnipay\Baifu\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * ExpressPurchaseResponse:
 * @date 16/1/18
 * @time 22:48
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressPurchaseResponse extends AbstractResponse implements RedirectResponseInterface {

    protected $endpoint = 'https://www.baifubao.com/api/0/pay/0/wapdirect/0';

    private $redirectMethod = 'GET';

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        $data = $this->getData();
        return $this->endpoint . '?' . http_build_query($data);
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        $this->redirectMethod;
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return [];
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return true;
    }

    public function isRedirect()
    {
        return true;
    }


    public function redirect()
    {
        Header('Location: ' . $this->getRedirectUrl());
    }


}