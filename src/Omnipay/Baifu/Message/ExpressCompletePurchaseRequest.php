<?php namespace Omnipay\Baifu\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * ExpressCompletePurchaseRequest:
 * @date 16/1/19
 * @time 01:07
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressCompletePurchaseRequest extends AbstractRequest {

    protected $endpoint = 'https://www.baifubao.com/api/0/query/0/pay_result_by_order_no';

    public $verifyResponse;

    protected function getParameter($key, $default = '')
    {
        return $this->parameters->get($key, $default);
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
            'key',
            'partner_id',
            'out_trade_no'
        );
        // $character_set = $this->getParameter('character_set', 'GBK');
        $params = [
            'service_code' => 11,
            'sp_no' => $this->getParameter('partner_id'),
            'order_no' => $this->getParameter('out_trade_no'),
            'output_type' => 1,
            'output_charset' => 1,
            'sign_method' => 1,
            'version' => 2
        ];
        ksort($params);
        $sign_str = '';
        foreach ($params as $k => $v)
        {
            $sign_str .= "$k=$v&";
        }
        $sign_str = $sign_str . 'key=' . $this->getParameter('key');
        $params['sign'] = md5($sign_str);
        return $params;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $verify_xml = $this->getVerifyResponse($data);
        /* <?xml version="1.0" encoding="gbk" ?> */
        $verify_xml = preg_replace('/<\?.*\?>/', '', $verify_xml);
        // $xml = simplexml_load_string($verify_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode(simplexml_load_string($verify_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->response = new ExpressCompletePurchaseResponse($this, $data);
    }

    protected function getVerifyResponse($data)
    {
        $verify_url = $this->endpoint;
        $verify_url = $verify_url . '?' . http_build_query($data) ;
        $responseTxt = $this->getHttpResponseGET($verify_url);
        return $responseTxt;
    }

    protected function getHttpResponseGET($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
        $responseText = curl_exec($curl);
        curl_close($curl);
        return $responseText;
    }
}