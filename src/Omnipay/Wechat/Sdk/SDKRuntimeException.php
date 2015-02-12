<?php namespace Omnipay\Wechat\Sdk;

/**
 * SDKRuntimeException:
 * @date 15/2/12
 * @time 23:33
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
class SDKRuntimeException extends \Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
} 