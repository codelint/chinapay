<?php namespace Omnipay\Utils;

/**
 * Utils:
 * @date 14/11/7
 * @time 00:09
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class Utils {

    static function load_helper()
    {
        require_once(__DIR__ . '/helper.php');
    }

    /**
     * format the wap alipay's notify on the xml value .notify_data
     * @param $params ['notify_data' => '']
     * @return array
     */
    static function format_wap_alipay_notify($params)
    {
//        $params = array_merge($get, $post);
        if (!empty($params['notify_data']))
        {
            $notify_data = $params['notify_data'];
            $doc = new \DOMDocument();
            $doc->loadXML($notify_data);
            if (!empty($doc->getElementsByTagName("notify")->item(0)->nodeValue))
            {
                $fields = array(
                    'notify_id', 'subject',
                    'out_trade_no', 'trade_no', 'trade_status', 'total_fee',
                    'buyer_email', 'buyer_id'
                );
                foreach ($fields as $f)
                {
                    $params[$f] = $doc->getElementsByTagName($f)->item(0)->nodeValue;
                }
            }
        }
        return $params;
    }


} 