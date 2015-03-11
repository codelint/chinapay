<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Wechat\Sdk\Notify;

/**
 * WechatCompleteRequest:
 * @date 14/11/8
 * @time 22:53
 * @author Ray.Zhang <ray@codelint.com>
 **/
class WechatCompletePurchaseRequest extends BaseAbstractRequest {


    function setAppId($appId)
    {
        $this->setParameter('app_id', $appId);
    }

    function getAppId()
    {
        return $this->getParameter('app_id');
    }

    function setAppKey($key)
    {
        $this->setParameter('app_key', $key);
    }

    function getAppKey()
    {
        return $this->getParameter('app_key');
    }


    function getPartner()
    {
        return $this->getParameter('partner');
    }

    function setPartner($id)
    {
        $this->setParameter('partner', $id);
    }

    function getPartnerKey()
    {
        return $this->getParameter('partner_key');
    }

    function setPartnerKey($key)
    {
        $this->setParameter('partner_key', $key);
    }

    function getBody()
    {
        return $this->getParameter('body', array());
    }

    function setBody($body)
    {
        $this->setParameter('body', $body);
    }

    function getRequestParams()
    {
        $this->getParameter('request_params', array());
    }

    function setRequestParams($rp)
    {
        $this->setParameter('request_params', $rp);
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
            'body'
        );
        return $this->getParameters();
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $body = $data['body'];
        $notify = new Notify();
        $notify->init(array(
            'app_id' => $data['app_id'],
            'mch_id' => $data['partner'],
            'app_secret' => '',
            'pay_sign_key' => $data['app_key'],
            'cert_path' => '',
            'cert_key_path' => '',
        ));
        $notify->saveData($body);

        if ($notify->checkSign() == FALSE)
        {
            $notify->setReturnParameter('return_code', 'FAIL');
            $notify->setReturnParameter('return_msg', '签名失败');
        }
        else
        {
            $notify->setReturnParameter('return_code', 'SUCCESS');
        }
        $returnXml = $notify->returnXml();

        $res_data['status'] = $notify->checkSign();
        $res_data['return_msg'] = $returnXml;
        $res_data['trade_status_ok'] = $notify->checkSign();

        return $this->response = new WechatCompletePurchaseResponse($this, $res_data);
    }

    /**
     * sort the query string
     * @param $qstr
     * @return array
     */
    static function sort($qstr)
    {
        $queryParts = explode('&', $qstr);

        $params = array();
        foreach ($queryParts as $param)
        {
            $item = explode('=', $param);
            $params [$item [0]] = $item [1];
        }
        ksort($params);
        return $params;
    }

    /**
     * like http_build_query(), but skip null value and do job after ksort
     *
     * @param $params
     * @return string
     *
     */
    static function http_build_query_without_null($params)
    {
        $buff = "";
        ksort($params);
        foreach ($params as $k => $v)
        {
            if (null != $v && "null" != $v && "sign" != $k)
            {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * like http_build_query(), ext: url encode the value and do job after ksort
     * @param $paraMap
     * @param $urlencode
     * @return string
     */
    static function formatBizQuery($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if ($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= strtolower($k) . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * sign with app_key
     * @param $params
     * @return string
     */
    protected function bizSign($params)
    {
        foreach ($params as $k => $v)
        {
            $bizParameters [strtolower($k)] = $v;
        }

        $bizParameters["appkey"] = $this->getAppKey();
        ksort($bizParameters);
        $bizString = static::formatBizQuery($bizParameters, false);
        return sha1($bizString);
    }

    /**
     * 检查signature是否一致
     * @param $body
     * @param $signature
     * @return bool
     */
    protected function verifyBody($body, $signature)
    {
        return $body && $signature && $signature == $this->bizSign($body);
    }

    /**
     * signature check, the params sign with partner key
     * @param $params
     * @return bool
     */
    protected function verifyParam($params)
    {
        $signStr = static::http_build_query_without_null($params);
        $stringSignTemp = $signStr . '&key=' . $this->getPartnerKey();
        $signValue = strtoupper(md5($stringSignTemp));

        return !empty($params['sign']) && $signValue == $params['sign'];
    }
}