<?php namespace Omnipay\Cmpay;

use Omnipay\Common\AbstractGateway;

/**
 * ExpressGateway:
 * @date 14/10/21
 * @time 上午10:35
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressGateway extends AbstractGateway {

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'CM_Express';
    }

    /**
     * Define gateway parameters, in the following format:
     *
     * array(
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => array('billing', 'login'), // enum variable, first item is default
     * );
     */
    public function getDefaultParameters()
    {
        return [
            'character_set' => '02',
            'sign_type' => 'MD5',
            'request_id' => time(),
            'type' => 'DirectPayConfirm',
            'version' => '2.0.0'
        ];
    }

    function setKey($filepath)
    {
        $this->setParameter('app_key', $filepath);
    }

    function getKey()
    {
        return $this->getParameter('app_key');
    }

    function getPartner()
    {
        return $this->getParameter('app_id');
    }

    function setPartner($id)
    {
        $this->setParameter('app_id', $id);
    }

    function getNotifyUrl()
    {
        return $this->getParameter('notify_url');
    }

    function setNotifyUrl($url)
    {
        $this->setParameter('notify_url', $url);
    }

    function setReturnUrl($url)
    {
        $this->setParameter('return_url', $url);
    }

    function getReturnUrl($url)
    {
        return $this->getParameter('return_url');
    }

    function setClientIP($ip)
    {
        $this->setParameter('client_ip', $ip);
    }

    function getClientIP()
    {
        return $this->getParameter('client_ip');
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Cmpay\Message\ExpressPurchaseRequest', $parameters);
    }

    public function completePurchase(array $params = array())
    {
        try
        {
            $merchantId = $params["merchantId"];
            $payNo = $params["payNo"];
            $returnCode = $params["returnCode"];
            $message = $params["message"];
            $signType = $params["signType"];
            $type = $params["type"];
            $version = $params["version"];
            $amount = $params["amount"];
            $amtItem = $params["amtItem"];
            $bankAbbr = $params["bankAbbr"];
            $mobile = $params["mobile"];
            $orderId = $params["orderId"];
            $payDate = $params["payDate"];
            $accountDate = $params["accountDate"];
            $reserved1 = $params["reserved1"];
            $reserved2 = $params["reserved2"];
            $status = $params["status"];
//            $payType = $params["payType"];
            $orderDate = $params["orderDate"];
            $fee = $params["fee"];
            $vhmac = $params["hmac"];

            if ($returnCode != 000000)
            {
                //此处表示后台通知产生错误
                // echo $returnCode . decodeUtf8($message);
                return [
                    'status' => false,
                    'message' => $returnCode . decodeUtf8($message)
                ];
            }

            $signData = $merchantId . $payNo . $returnCode . $message
                . $signType . $type . $version . $amount
                . $amtItem . $bankAbbr . $mobile . $orderId
                . $payDate . $accountDate . $reserved1 . $reserved2
                . $status . $orderDate . $fee;
            $hmac = Utils::MD5sign($this->getParameter('app_key'), $signData);

            if ($hmac != $vhmac)
                //此处无法信息数据来自手机支付平台
                return [
                    'status' => false,
                    'message' => '验签失败'
                ];
            else
            {
                return [
                    'status' => true,
                    'message' => 'SUCCESS'
                ];
            }
        } catch (\Exception $e)
        {
            return ['status' => false, 'message' => $e->getLine() . ':' . $e->getMessage()];
        }
    }
}
