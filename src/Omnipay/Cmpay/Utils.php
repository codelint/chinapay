<?php namespace Omnipay\Cmpay;

/**
 * Utils:
 * @date 14/10/21
 * @time 下午12:14
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class Utils {


    /*
     功能 发送HTTP请求
      URL  请求地址
      data 请求数据数组
    */
    static function POSTDATA($url, $data)
    {
        $url = parse_url($url);
        if (!$url)
        {
            return "couldn't parse url";
        }
        if (!isset($url['port']))
        {
            $url['port'] = "";
        }

        if (!isset($url['query']))
        {
            $url['query'] = "";
        }


        $encoded = "";

        while (list($k, $v) = each($data))
        {
            $encoded .= ($encoded ? "&" : "");
            $encoded .= rawurlencode($k) . "=" . rawurlencode($v);
        }
        $urlHead = null;
        $urlPort = $url['port'];
        if ($url['scheme'] == "https")
        {
            $urlHead = "ssl://" . $url['host'];
            if ($url['port'] == null || $url['port'] == 0)
            {
                $urlPort = 443;
            }
        }
        else
        {
            $urlHead = $url['host'];
            if ($url['port'] == null || $url['port'] == 0)
            {
                $urlPort = 80;
            }
        }
        $fp = fsockopen($urlHead, $urlPort);

        if (!$fp) return "Failed to open socket to $url[host]";

        $tmp = "";
        $tmp .= sprintf("POST %s%s%s HTTP/1.0\r\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
        $tmp .= "Host: $url[host]\r\n";
        $tmp .= "Content-type: application/x-www-form-urlencoded\r\n";
        $tmp .= "Content-Length: " . strlen($encoded) . "\r\n";
        $tmp .= "Connection: close\r\n\r\n";
        $tmp .= "$encoded\r\n";
        fputs($fp, $tmp);

        $line = fgets($fp, 1024);

        if (!preg_match("#^HTTP/1\.. 200#i", $line))
        {
            $logstr = "MSG" . $line;
            return array("FLAG" => 0, "MSG" => $line);
        }

        $results = "";
        $inheader = 1;
        while (!feof($fp))
        {
            $line = fgets($fp, 1024);
            if ($inheader && ($line == "\n" || $line == "\r\n"))
            {
                $inheader = 0;
            }
            elseif (!$inheader)
            {
                $results .= $line;
            }
        }
        fclose($fp);
        return array("FLAG" => 1, "MSG" => $results);
    }

    //MD5方式签名
    static function MD5sign($okey, $odata)
    {
        $signdata = static::hmac("", $odata);
        return static::hmac($okey, $signdata);
    }

    static function hmac($key, $data)
    {
        $key = iconv('gb2312', 'utf-8', $key);
        $data = iconv('gb2312', 'utf-8', $data);
        $b = 64;
        if (strlen($key) > $b)
        {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    /*
     功能 把http请求返回数组 格式化成数组
    */
    static function parseRecv($source)
    {
        $ret = array();
        $temp = explode("&", $source);

        foreach ($temp as $value)
        {
            $index = strpos($value, "=");
            $_key = substr($value, 0, $index);
            $_value = substr($value, $index + 1);
            $ret[$_key] = $_value;
        }

        return $ret;
    }

    /*
    	功能：把UTF-8 编号数据转换成 GB2312 忽略转换错误
    */
    static function decodeUtf8($source)
    {
        $temp = urldecode($source);
        $ret = iconv("UTF-8", "GB2312//IGNORE", $temp);
        return $ret;
    }

    /*获取用户IP地址*/
    static function getClientIP()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if (!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = "unknown";
        }
        return $cip;

    }

    //返回URL处理
    static function parseUrl($payUrl)
    {
        $temp = explode("<hi:$$>", $payUrl);
        $url_lst = explode("<hi:=>", $temp[0]);
        $url = $url_lst[1];
        $method_lst = explode("<hi:=>", $temp[1]);
        $method = $method_lst[1];
        $sessionid_lst = explode("<hi:=>", $temp[2]);
        $sessionid = $sessionid_lst[1];
        $url = $url . "?SESSIONID=" . $sessionid;
        $rpayUrl = array();
        $rpayUrl["url"] = $url;
        $rpayUrl["method"] = $method;
        return $rpayUrl;
    }
}
