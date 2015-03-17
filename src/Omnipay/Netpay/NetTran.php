<?php namespace Omnipay\Netpay;
/**
 * 广州好易联支付网络有限公司支付网关商户接口PHP4.4.8
 * @author 技术开发部 邵静
 * @version 3.4
 * @date 2008.5.15
 */
/**
 * @modifier Ray.Zhang <codelint@foxmail.com>
 * @date 2014.3.18
 */
DEFINE("OPEN_SSL_CONF_PATH", "~/openssl.cnf"); //point to your config file
DEFINE("OPEN_SSL_CERT_DAYS_VALID", 365); //1 year
DEFINE("OPEN_SSL_IS_FILE", 1);

/**
 * 对数据进行加密、解密、签名、验证签名
 */
class NetTran {

//    private $apiUrl = 'http://test.gnete.com:8888/bin/scripts/OpenVendor/gnete/V34/GetPayResult.asp';
    private $apiUrl = 'https://www.gnete.com/bin/scripts/OpenVendor/gnete/V34/GetOvOrder.asp';

    /**
     * @var 取出上次调用加密、解密、签名函数成功后的输出结果
     */
    var $LastResult;

    /**
     * @var 取出上次调用任何函数失败后的失败原因
     */
    var $LastErrMsg;

    /**
     * @var string 取出本开发包的当前版本
     */
    var $CurrVer = "广州好易联支付网络有限公司支付网关商户接口 版本号:3.4.0.1 最后编译日期:2005-10-14";

    /**
     * 生成随机数
     * @param $keyLen 随机数长度
     * @return string 随机字符串
     */
    function GenKey($keyLen)
    {
        $tempstring = "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef";
        $temp = str_shuffle($tempstring);
        //print($temp);//????
        $start = rand(0, strlen($tempstring) - $keyLen);
        return substr($temp, $start, $keyLen);

    }

    /**
     * 对字符串进行加密
     * @param string $TobeEncrypted 待加密的字符串
     * @param string $CertFile 解密者公钥证书路径
     * @return bool 加密成功返回true(从LastResult属性获取结果)，失败返回false(从LastErrMsg属性获取失败原因)
     */
    function EncryptMsg($TobeEncrypted, $CertFile)
    {

        //读证书文件取出公钥
        $fp = fopen($CertFile, "r");
        if (!$fp)
        {
            $this->LastErrMsg = "Error Number:-10005, Error Description: ER_FIND_CERT_FAILED（找不到证书）";
            return false;
        }
        $pub_key = fread($fp, 8192);
        fclose($fp);

        $keyLen = 128;
        //RSA当待加密串长度超出128-11字节时，需使用长数据加密方式，否则使用自带的加密
        if (strlen($TobeEncrypted) > $keyLen - 11)
        {
            $cipher = MCRYPT_3DES; //MCRYPT_3DES;
            $mode = 'nofb';
            $td = mcrypt_module_open($cipher, "", $mode, "");

            $key_hex = $this->GenKey(mcrypt_enc_get_key_size($td) * 2);;
            $key = pack("H" . strlen($key_hex), $key_hex);
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

            mcrypt_generic_init($td, $key, $iv);
            $encrypted_data = mcrypt_generic($td, $TobeEncrypted);

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            //将KEY用RSA公钥加密
            if (openssl_public_encrypt($key, $encryptedKey, $pub_key))
            {
                //iv+加密后的KEY+加密后的数据
                $this->LastResult = bin2hex($iv) . bin2hex($encryptedKey) . bin2hex($encrypted_data);
                return true;

            } else
            {
                $this->LastErrMsg = "Error Number:-10022, Error Description: ER_ENCRYPT_ERROR（加密失败）|" . openssl_error_string();
                return false;
            }
        } else
        {
            if (openssl_public_encrypt($TobeEncrypted, $crypttext, $pub_key))
            {
                $this->LastResult = bin2hex($crypttext);
                return true;
            } else
            {
                $this->LastErrMsg = "Error Number:-10022, Error Description: ER_ENCRYPT_ERROR（加密失败）|" . openssl_error_string();
                return false;
            }
        }
    }

    /**
     * 对加密后的密文进行解密
     * @param $TobeDecrypted    需要解密的密文
     * @param $KeyFile pem证书文件路径
     * @param $PassWord 私钥保护密码
     * @return bool 解密成功返回true(从LastResult属性获取结果)，失败返回false(从LastErrMsg属性获取失败原因)
     */
    function DecryptMsg($TobeDecrypted, $KeyFile, $PassWord)
    {
        //读文件取出私钥
        $fp = fopen($KeyFile, "r");
        if (!$fp)
        {
            $this->LastErrMsg = "Error Number:-10005, Error Description: ER_FIND_CERT_FAILED（找不到证书）";
            return false;
        }
        $pri_key = fread($fp, 8192);
        fclose($fp);

        $keyLen = 128;
        //当解密密文长度>256时，需使用长数据解密方式，否则使用自带的解密
        if (strlen($TobeDecrypted) > $keyLen * 2)
        {
            $iv_hex = substr($TobeDecrypted, 0, 16);
            $key_hex = substr($TobeDecrypted, 16, 256);
            $encrypted_hex = substr($TobeDecrypted, 16 + 256);
            //print($iv_hex . "<br>" . $key_hex . "<br>" . $encrypted_hex . "<br>");

            $key = pack("H" . strlen($key_hex), $key_hex); //解密前的KEY
            $iv = pack("H" . strlen($iv_hex), $iv_hex);
            $encrypted = pack("H" . strlen($encrypted_hex), $encrypted_hex);

            //用私钥解密KEY
            $res = openssl_get_privatekey($pri_key, $PassWord);
            if (!openssl_private_decrypt($key, $decryptedKey, $res))
            {
                $this->LastErrMsg = "Error Number:-10015, Error Description: ER_PRIKEY_CANNOT_FOUND（没有找到匹配私钥）|" . openssl_error_string();
                return false;
            }


            //3DES解密
            $cipher = MCRYPT_3DES; //MCRYPT_3DES;
            $mode = 'nofb';

            $td = mcrypt_module_open($cipher, '', $mode, '');
            if (mcrypt_generic_init($td, $decryptedKey, $iv) < 0)
            {
                $this->LastErrMsg = "Error Number:-10023, Error Description: ER_DECRYPT_ERROR（解密失败）|";
                return false;
            }

            $decryptedText = mdecrypt_generic($td, $encrypted);

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $this->LastResult = $decryptedText;
            return true;
        } else
        {
            //用私钥解密
            $res = openssl_get_privatekey($pri_key, $PassWord);
            if (openssl_private_decrypt($TobeDecrypted, $decryptedText, $pri_key))
            {
                $this->LastResult = bin2hex($decryptedText);
                return true;
            } else
            {
                $this->LastErrMsg = "Error Number:-10023, Error Description: ER_DECRYPT_ERROR（解密失败）|" . openssl_error_string();
                return false;
            }
        }
    }

    /**
     * 对字符串进行签名
     * @param string $TobeSigned 需要进行签名的字符串
     * @param string $KeyFile PFX证书文件路径
     * @param string $PassWord 私钥保护密码
     * @return bool 签名成功返回true(从LastResult属性获取结果)，失败返回false(从LastErrMsg属性获取失败原因)
     */
    function SignMsg($TobeSigned, $KeyFile, $PassWord)
    {
        //读文件取出私钥
        $fp = fopen($KeyFile, "r");
        if (!$fp)
        {
            $this->LastErrMsg = "Error Number:-10005, Error Description: ER_FIND_CERT_FAILED（找不到证书）";
            return false;
        }
        $pri_key = fread($fp, 8192);
        fclose($fp);
        $res = openssl_get_privatekey($pri_key, $PassWord);
        if (openssl_sign($TobeSigned, $signature, $res))
        {
            $SignedMsg = bin2hex($signature);
            $this->LastResult = $SignedMsg;
            return true;

        } else
        {
            $this->LastErrMsg = "Error Number:-10020, Error Description: ER_SIGN_ERROR（签名失败）|" . openssl_error_string();
            return false;
        }
    }

    /**
     * 验证签名
     * @param $TobeVerified 待验证签名的密文
     * @param $PlainText 待验证签名的明文
     * @param $CertFile 签名者公钥证书
     * @return bool 验证成功返回true，失败返回false(从LastErrMsg属性获取失败原因)
     */
    function VerifyMsg($TobeVerified, $PlainText, $CertFile)
    {
        //用公钥验签
        $fp = fopen($CertFile, "r");
        if (!$fp)
        {
            $this->LastErrMsg = "Error Number:-10005, Error Description: ER_FIND_CERT_FAILED（找不到证书）";
            return false;
        }
        $pub_key = fread($fp, 8192);
        fclose($fp);

        $res = openssl_get_publickey($pub_key);
        if (openssl_verify($PlainText, pack("H" . strlen($TobeVerified), $TobeVerified), $res))
        {
            //print("验证成功"." <br>");
            return true;
        } else
        {
            $this->LastErrMsg = "Error Number:-10021, Error Description: ER_VERIFY_ERROR（验签失败）|" . openssl_error_string();
            return false;
        }
    }

    /**
     * 返回上次调用加密、解密、签名函数成功后的输出结果
     * @return 返回上次调用加密、解密、签名函数成功后的输出结果
     */
    function getLastResult()
    {
        return $this->LastResult;
    }

    /**
     * 返回上次调用任何函数失败后的失败原因
     * @return 返回上次调用任何函数失败后的失败原因
     */
    function getLastErrMsg()
    {
        return $this->LastErrMsg;
    }

    /**
     * 返回本开发包的当前版本
     * @return string 返回本开发包的当前版本
     */
    function getCurrVer()
    {
        return $this->CurrVer;
    }

    /**
     * 从好易联支付网关下载符合条件的交易结果数据
     * @param $MerId    好易联商户ID
     * @param $UserId 好易联对帐用户ID
     * @param $Pwd 好易联对帐用户密码
     * @param $PaySuc 交易结果类型(0-失败订单，1-成功订单，2-全部订单)
     * @param $ShoppingTime 交易日期(查询ShoppingTime到现在的交易结果数据，此域不为空时，BeginTime及EndTime失效，格式为：yyyy-mm-dd hh:mm:ss)
     * @param $BeginTime 开始时间(查询BeginTime到EndTime的交易结果数据，格式为：yyyy-mm-dd hh:mm:ss)
     * @param $EndTime 结束时间(查询BeginTime到EndTime的交易结果数据，格式为：yyyy-mm-dd hh:mm:ss)
     * @param $OrderNo 订单号
     * @return bool 下载成功返回true(从LastResult属性获取结果)，失败返回false(从LastErrMsg属性获取失败原因)
     */
    public function GetResult($MerId, $UserId, $Pwd, $PaySuc, $ShoppingTime, $BeginTime, $EndTime, $OrderNo)
    {
        //从配置文件中读出目标页面URL
        $urlString = $this->apiUrl;

        //组合查询参数
        $postString = "MerId=" . $MerId .
            "&UserId=" . $UserId .
            "&Pwd=" . $Pwd .
            "&PaySuc=" . $PaySuc .
            "&ShoppingTime=" . $ShoppingTime .
            "&BeginTime=" . $BeginTime .
            "&EndTime=" . $EndTime .
            "&OrderNo=" . $OrderNo;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $urlString);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($ch);

        if (curl_errno($ch) <> 0)
        {
            $this->LastErrMsg = "Error Number:-00006, Error Description: GET_RESULT_ERROR（获取结果失败:" . curl_errno($ch);
            curl_close($ch);
            return false;
        } else
        {
            curl_close($ch);
            if (strlen($res) == 0)
            {
                $this->LastErrMsg = "Error Number:-00001, Error Description: RETURN_BLANK（远程服务器返回空页面）";
                return false;
            } else
            {
                if (substr_count($res, "\n") > 0)
                {
                    $this->LastResult = $res;
                    return true;
                } else
                {
                    $this->LastErrMsg = $res;
                    return false;
                }
            }
        }
    }
}
	
