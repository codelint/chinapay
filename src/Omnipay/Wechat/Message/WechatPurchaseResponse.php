<?php namespace Omnipay\Wechat\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

use Omnipay\Wechat\Sdk\CommonUtil;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * WechatPurchaseResponse:
 * @date 14-6-4
 * @time 下午3:34
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class WechatPurchaseResponse extends BaseAbstractResponse implements RedirectResponseInterface {

    protected $endpoint = 'weixin://wxpay/bizpayurl';

    /**
     * @var bool|string
     */
    protected $return_url = false;

    protected $cancel_url = false;

    protected $fail_url = false;

    public function isRedirect()
    {
        return true;
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        $params = $this->getData();
        $key = $params['appkey'];
        $params = array_except($params, 'appkey');
        if(!empty($params['productid']))
        {
            $params['product_id'] = $params['productid'];
        }
        $params = array_only($params, ['appid', 'mch_id', 'product_id']);
        $params['time_stamp'] = time();
        $params['nonce_str'] = str_random(8);
        $sign = CommonUtil::gen_signature($params, $key);
        ksort($params);
        $url = $this->endpoint . '?sign=' . $sign . '&' . http_build_query($params);
        return $url;
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        $params = array_except($this->getData(), 'productid');

        $key = $params['appkey'];

        $params = [
            'appId' => $params['appid'],
            'package' => $params['package'],
            'timeStamp' => '' . $params['timestamp'],
            'nonceStr' => $params['noncestr'],
            'signType' => 'MD5'
        ];
        $params['paySign'] = CommonUtil::gen_signature($params, $key);

        return $params;
    }

    public function redirect($content_type = 'html')
    {
//        $agent = strtolower(array_get($_SERVER, 'HTTP_USER_AGENT', ''));
//        if(str_contains($agent, 'iphone')){
//            return parent::redirect();
//        }
        $data = $this->getRedirectData();
        $return_url = $this->return_url ? : array_get($_SERVER, 'HTTP_REFERER', false);
        $refered_url = array_get($_SERVER, 'HTTP_REFERER', $return_url);
        $cancel_url = $this->cancel_url ? : $refered_url;
        $fail_url = $this->fail_url ?: $refered_url;

        switch ($content_type)
        {
            case 'html':
                $output = $this->redirect_html();
                break;
            case 'js':
            case 'javascript':
                $output = $this->redirect_js();
                break;
            default:
                $output = $this->redirect_html();
        }

        $output = sprintf($output, json_encode($data), $return_url, $cancel_url, $fail_url);

        return HttpResponse::create($output)->send();
    }

    protected function redirect_html()
    {
        return '<!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>微信安全支付</title>
            </head>
            <body style="font-size: 3em;width: 320px;">
                <p>付款中...</p>
            </body>
            <script type="text/javascript">
            ' . $this->redirect_js() . '
            </script>
        </html>';
    }

    protected function redirect_js()
    {
        return '(function(){
        var data = %1$s;

        setTimeout(function(){
            if(typeof WeixinJSBridge == "undefined"){
                return setTimeout(arguments.callee, 200);
            }
            WeixinJSBridge.invoke(
                "getBrandWCPayRequest",data,function(res){
                // 返回 res.err_msg,取值
                // get_brand_wcpay_request:cancel 用户取消
                // get_brand_wcpay_request:fail 发送失败
                // get_brand_wcpay_request:ok 发送成功
               if(res.err_msg == "get_brand_wcpay_request:ok"){
                    alert("支付成功");
                    window.location.href = "%2$s";
               }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                    alert("支付取消");
                    window.location.href = "%3$s";
                }else{
                    alert("支付失败(" + res["err_msg"] + ")");
                    window.location.href = "%4$s";
                }
                //WeixinJSBridge.log(res.err_msg); alert(res.err_code+res.err_desc);
            });

        }, 200);
        })();';
    }

    public function setReturnUrl($url)
    {
        $this->return_url = $url;
    }

    public function setCancelUrl($url)
    {
        $this->cancel_url = $url;
    }

    public function setFailUrl($url)
    {
        $this->fail_url = $url;
    }
}
