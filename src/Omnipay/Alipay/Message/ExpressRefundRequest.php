<?php
/**
 * User: Ray.Zhang <codelint@foxmail.com>
 * Date: 14/12/29
 * Time: 下午3:04
 */

namespace Omnipay\Alipay\Message;


use Omnipay\Common\Message\ResponseInterface;

class ExpressRefundRequest extends BasePurchaseRequest {

    protected function validateData()
    {
        return $this->validate(
            'service',
            'partner',
            'key',
            'seller_email',
            'refund_data',
            'notify_url'
            );
    }

    function getRefundData()
    {
        return $this->parameters->get('refund_data');
    }

    function setRefundData($data)
    {
        $this->parameters->set('refund_data', $data);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validateData();
        $detail = $this->getRefundData();

        foreach ($detail as &$v)
        {
            $v = $v['trade_no'] . '^' . $v['total_fee'] . '^' . $v['memo'];
        }
        $data = array(
            "service" => $this->getService(),
            "partner" => $this->getPartner(),
            "seller_user_id" => $this->getPartner(),
            "refund_date" => date('Y-m-d H:i:s'),
            "batch_no" => date('YmdHi') . rand(10000, 99999),
            'batch_num' => count($detail),
            'detail_data' => implode('#', $detail),
            "notify_url" => $this->getNotifyUrl(),
            "seller_email" => $this->getSellerEmail(),
            "_input_charset" => $this->getInputCharset(),
        );
        $data = array_filter($data);
        $data['sign'] = $this->getParamsSignature($data);
        $data['sign_type'] = $this->getSignType();
        return $data;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new ExpressRefundResponse($this, $data);
    }
}