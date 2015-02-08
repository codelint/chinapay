<?php namespace Omnipay\Cmpay\Message;

use Omnipay\Cmpay\Utils;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * ExpressPurchaseRequest:
 * @date 14/10/21
 * @time 上午11:18
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressPurchaseRequest extends AbstractRequest {

    protected function getParameter($key)
    {
        return $this->parameters->get($key, '');
    }

    public function initialize(array $parameters = array())
    {
        if (null !== $this->response)
        {
            throw new \RuntimeException('Request cannot be modified after it has been sent!');
        }

        $this->parameters = new ParameterBag;
        foreach ($parameters as $k => $v)
        {
            $this->parameters->set($k, $v);
        }
        return $this;
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
            'app_id',
            'app_key',
            'character_set',
            'sign_type',
            'type',
            'version',
            'out_trade_no',
            'subject',
            'total_fee'
        );
        $signKey = $this->getParameter('app_key');

        $characterSet = $this->getParameter('character_set');
        $callbackUrl = $this->getParameter('return_url');
        $notifyUrl = $this->getParameter('notify_url');
        $ipAddress = $this->getParameter('client_ip');
        $merchantId = $this->getParameter('app_id');
        $requestId = $this->getParameter('request_id') ?: $this->getParameter('out_trade_no');
        $signType = $this->getParameter('sign_type');
        $type = $this->getParameter('type');
        $version = $this->getParameter('version');
        $amount = $this->getParameter('total_fee') * 100;
        $bankAbbr = $this->getParameter('bank');
        $currency = '00';
        $orderDate = date('Ymd');
        $orderId = $this->getParameter('out_trade_no');
        $merAcDate = date('Ymd');
        $period = $this->getParameter('expired_time') ?: 10;
        $periodUnit = '00';
        $merchantAbbr = $this->getParameter('merchant_abbr');
        $productId = $this->getParameter('product_id') ?: $this->getParameter('out_trade_no');
        $productName = iconv('utf-8', 'gb2312', $this->getParameter('subject'));
        $productDesc = iconv('utf-8', 'gb2312', $this->getParameter('summary'));
//        $productName = $this->getParameter('subject');
//        $productDesc =  $this->getParameter('summary');
        $productNum = 1;
        $reserved1 = '';
        $reserved2 = '';
        $userToken = '';
        $showUrl = '';
        $couponsFlag = '';

        $signData = $characterSet . $callbackUrl . $notifyUrl . $ipAddress
            . $merchantId . $requestId . $signType . $type
            . $version . $amount . $bankAbbr . $currency
            . $orderDate . $orderId . $merAcDate . $period . $periodUnit
            . $merchantAbbr . $productDesc . $productId . $productName
            . $productNum . $reserved1 . $reserved2 . $userToken
            . $showUrl . $couponsFlag;

        $hmac = Utils::MD5sign($signKey, $signData);

        $requestData = array();
        $requestData["characterSet"] = $characterSet;
        $requestData["callbackUrl"] = $callbackUrl;
        $requestData["notifyUrl"] = $notifyUrl;
        $requestData["ipAddress"] = $ipAddress;
        $requestData["merchantId"] = $merchantId;
        $requestData["requestId"] = $requestId;
        $requestData["signType"] = $signType;
        $requestData["type"] = $type;
        $requestData["version"] = $version;
        $requestData["hmac"] = $hmac;
        $requestData["amount"] = $amount;
        $requestData["bankAbbr"] = $bankAbbr;
        $requestData["currency"] = $currency;
        $requestData["orderDate"] = $orderDate;
        $requestData["orderId"] = $orderId;
        $requestData["merAcDate"] = $merAcDate;
        $requestData["period"] = $period;
        $requestData["periodUnit"] = $periodUnit;
        $requestData["merchantAbbr"] = $merchantAbbr;
        $requestData["productDesc"] = $productDesc;
        $requestData["productId"] = $productId;
        $requestData["productName"] = $productName;
        $requestData["productNum"] = $productNum;
        $requestData["reserved1"] = $reserved1;
        $requestData["reserved2"] = $reserved2;
        $requestData["userToken"] = $userToken;
        $requestData["showUrl"] = $showUrl;
        $requestData["couponsFlag"] = $couponsFlag;
        $requestData['merchantKey'] = $signKey;



        return $requestData;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $this->response = new ExpressPurchaseResponse($this, $data);
//        if ($this->parameters->has('return_url'))
//        {
//            $this->response->setReturnUrl($this->parameters->get('return_url'));
//        }
        return $this->response;
    }

}
