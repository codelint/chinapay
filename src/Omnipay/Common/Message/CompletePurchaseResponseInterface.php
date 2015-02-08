<?php namespace Omnipay\Common\Message;

/**
 * CompletePurchaseResponseInterface: 
 * @date 14-4-1
 * @time 下午5:45
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
interface CompletePurchaseResponseInterface {
    /**
     * 返回是否交易成功
     * @return bool
     */
    public function isTradeStatusOk();
}
