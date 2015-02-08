<?php namespace Omnipay\Cmpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Cmpay\Utils;

/**
 * ExpressPurchaseResponse:
 * @date 14/10/21
 * @time 上午11:18
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class ExpressPurchaseResponse extends AbstractResponse implements RedirectResponseInterface {

    protected $endpoint = 'https://ipos.10086.cn/ips/cmpayService';

    private $redirectMethod = 'GET';

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        $data = $this->getData();
        $params = array_except($data, 'merchantKey');
        $merchantKey = $data['merchantKey'];

        $sTotalString = Utils::POSTDATA($this->endpoint, $params);

        $recv = $sTotalString["MSG"];
        $recv = iconv('gb2312', 'utf-8', $recv);
        $recvArray = Utils::parseRecv($recv);

        $code = $recvArray["returnCode"];
        $payUrl = false;
        if ($code != "000000")
        {
            // echo "code:" . $code . "</br>msg:" . decodeUtf8($recvArray["message"]);
            return $code.':'.$recvArray["message"];
        }
        else
        {
            $vfsign = $recvArray["merchantId"] . $recvArray["requestId"]
                . $recvArray["signType"] . $recvArray["type"]
                . $recvArray["version"] . $recvArray["returnCode"]
                . $recvArray["message"] . $recvArray["payUrl"];
            $hmac = Utils::MD5sign($merchantKey, $vfsign);
            $vhmac = $recvArray["hmac"];
            if ($hmac != $vhmac)
            {
                echo "验证签名失败!";
                exit();
            }
            else
            {
                $payUrl = $recvArray["payUrl"];
                //返回url处理
                $rpayUrl = Utils::parseUrl($payUrl);
                $payUrl = $rpayUrl['url'];
                $this->redirectMethod = $rpayUrl['method'];
            }
        }
        return $payUrl;
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return $this->redirectMethod;
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return [];
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return true;
    }

    public function isRedirect()
    {
        return true;
    }


}
