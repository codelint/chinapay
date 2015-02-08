<?php namespace Omnipay\Netpay\Message;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * NetpayCompletePurchaseRequest: 
 * @date 14-3-18
 * @time 下午2:55
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class NetpayCompletePurchaseRequest extends AbstractRequest {

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        // TODO: Implement getData() method.
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        // TODO: Implement sendData() method.
    }
}
