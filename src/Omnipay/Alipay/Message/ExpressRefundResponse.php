<?php
/**
 * User: Ray.Zhang <codelint@foxmail.com>
 * Date: 14/12/29
 * Time: 下午3:05
 */

namespace Omnipay\Alipay\Message;


class ExpressRefundResponse extends PurchaseResponse{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        // TODO: Implement isSuccessful() method.
    }

    public function isRedirect()
    {
        return true;
    }



}