<?php namespace Omnipay\Netpay\Message;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Netpay\NetTran;

/**
 * NetpayPurchaseRequest:
 * @date 14-3-18
 * @time 下午2:55
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class NetpayPurchaseRequest extends BaseAbstractRequest {

    protected $endpoint = 'https://www.gnete.com/bin/scripts/OpenVendor/gnete/V34/GetOvOrder.asp';
//    protected $endpoint = 'http://test.gnete.com:8888/Bin/Scripts/OpenVendor/Gnete/V34/GetOvOrder.asp';

    function getEndPoint()
    {
        return $this->endpoint;
    }

    function setEndPoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate(
            'partner',
            'out_trade_no',
            'total_fee',
            'notifyUrl'
        );

        $text = 'MerId=' . $this->getPartner() . '&' .
            'OrderNo=' . $this->getOutTradeNo() . '&' .
            'OrderAmount=' . $this->getTotalFee() . '&' .
            'CurrCode=' . $this->getCurrency() . '&' .
            'CallBackUrl=' . $this->getNotifyUrl() . '&' .
            'BankCode=' . $this->getBankCode() . '&' .
            'ResultMode=' . $this->getResultMode() . '&' .
            'Reserved01=' . $this->getDescription() . '&' .
            'Reserved02=';
        $obj = new NetTran();

        $encodeMsg = $obj->EncryptMsg($text, $this->getCertFile()) ? $obj->getLastResult() : '';
        $signedMsg = $obj->SignMsg($text, $this->getKeyFile(), "12345678") ? $obj->getLastResult() : '';
        return [
            'EncodeMsg' => $encodeMsg,
            'SignMsg' => $signedMsg
        ];
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new NetpayPurchaseResponse($this, $data);
    }
}
