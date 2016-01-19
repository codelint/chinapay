<?php namespace Omnipay\Baifu\Message;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\CompletePurchaseResponseInterface;

/**
 * ExpressCompletePurchaseResponse: 
 * @date 16/1/19
 * @time 01:07
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressCompletePurchaseResponse extends AbstractResponse implements CompletePurchaseResponseInterface{

    /**
     * 返回是否交易成功
     * @return bool
     */
    public function isTradeStatusOk()
    {
        $data = $this->getData();
        return ($data['pay_result'] == 1);
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return !empty($this->data);
    }

    public function getTransactionReference()
    {
        return $this->getData();
    }


}