<?php namespace Omnipay\Netpay\Message;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\CompletePurchaseResponseInterface;

/**
 * NetpayCompletePurchaseResponse: 
 * @date 14-4-1
 * @time 下午5:48
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class NetpayCompletePurchaseResponse extends AbstractResponse implements CompletePurchaseResponseInterface {

    /**
     * 返回是否交易成功
     * @return bool
     */
    public function isTradeStatusOk()
    {
        return false;
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
