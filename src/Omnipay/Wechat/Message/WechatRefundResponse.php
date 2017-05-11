<?php namespace Omnipay\Wechat\Message;

/**
 * WechatRefundResponse:
 * @date 2017/5/11
 * @time 15:59
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatRefundResponse extends BaseAbstractResponse {

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
            'transaction_id', 'out_trade_no', 'out_refund_no',
            'refund_id', 'refund_fee', 'total_fee', 'cash_fee', 'cash_refund_fee',
            'appid', 'mch_id', 'device_info', 'nonce_str',
            'sign',
            'result_code', 'err_code', 'err_code_des', 'return_code', 'return_msg'
        ));
    }
}