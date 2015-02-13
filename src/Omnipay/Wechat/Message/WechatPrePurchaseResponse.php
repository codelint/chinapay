<?php namespace Omnipay\Wechat\Message;

/**
 * WechatPrePurchaseResponse:
 * @date 15/2/12
 * @time 00:50
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatPrePurchaseResponse extends BaseAbstractResponse {

    public function isRedirect()
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
        return $this->data['return_code'] == 'SUCCESS' && $this->data['result_code'] == 'SUCCESS';
    }

    public function getTransactionReference()
    {
        return array_only($this->data, array(
            'prepay_id', 'trade_type', 'code_url',
            'appid', 'mch_id', 'device_info', 'nonce_str',
            'sign',
            'result_code', 'err_code', 'err_code_des', 'return_code', 'return_msg'
        ));
    }
}