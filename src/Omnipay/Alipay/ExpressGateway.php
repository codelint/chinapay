<?php

namespace Omnipay\Alipay;

/**
 * Class ExpressGateway
 *
 * @package Omnipay\Alipay
 */
class ExpressGateway extends BaseAbstractGateway
{

    protected $service_name = 'create_direct_pay_by_user';

    protected $refund_service_name = 'refund_fastpay_by_platform_pwd';

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'Alipay Express';
    }

    public function purchase(array $parameters = array())
    {
        $this->setService($this->service_name);
        return $this->createRequest('\Omnipay\Alipay\Message\ExpressPurchaseRequest', $parameters);
    }

    /**
     * support refund for express pay
     * @param array $parameters exp: ['refund_data' => [ [ 'trade_no' => 'alipay trade no', 'total_fee' => 'the fee to refund', 'memo' => ''] ] ]
     */
    public function refund(array $parameters = array())
    {
        $this->setService($this->refund_service_name);
        return $this->createRequest('\Omnipay\Alipay\Message\ExpressRefundRequest', $parameters);
    }
}
