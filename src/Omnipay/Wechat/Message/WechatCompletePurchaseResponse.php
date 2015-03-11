<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\CompletePurchaseResponseInterface;

/**
 * WechatCompleteResponse:
 * @date 14/11/8
 * @time 22:55
 * @author Ray.Zhang <ray@codelint.com>
 **/
class WechatCompletePurchaseResponse extends BaseAbstractResponse implements CompletePurchaseResponseInterface {

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['status'];
    }

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * 返回是否交易成功
     * @return bool
     */
    public function isTradeStatusOk()
    {
        return $this->data['trade_status_ok'];
    }

    public function getMessage()
    {
        return $this->data['return_msg'];
    }


}