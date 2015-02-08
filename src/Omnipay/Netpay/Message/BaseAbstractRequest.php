<?php namespace Omnipay\Netpay\Message;
use Omnipay\Common\Message\AbstractRequest;

/**
 * BaseAbstractRequest:
 * @date 14-3-18
 * @time 下午4:01
 * @author Ray.Zhang <codelint@foxmail.com>
 **/
abstract class BaseAbstractRequest extends AbstractRequest {
    private $keyFile = '';
    private $certFile = '';

    function setKeyFile($filepath)
    {
        $this->keyFile = $filepath;
    }

    function getKeyFile()
    {
        return $this->keyFile;
    }

    function setCertFile($filepath)
    {
        $this->certFile = $filepath;
    }

    function getCertFile()
    {
        return $this->certFile;
    }

    function getPartner()
    {
        return $this->getParameter('partner');
    }

    function setPartner($id)
    {
        $this->setParameter('partner', $id);
    }

    public function getTotalFee()
    {
        return $this->getParameter('total_fee');
    }

    public function setTotalFee($value)
    {
        $this->setParameter('total_fee', $value);
    }

    function getCurrency()
    {
        return $this->getParameter('currency') ? : 'CNY';
    }

    public function getOutTradeNo()
    {
        return $this->getParameter('out_trade_no');
    }

    public function setOutTradeNo($value)
    {
        $this->setParameter('out_trade_no', $value);
    }

    function getBankCode()
    {
        return $this->getParameter('bank') ? : '';
    }

    function setBankCode($bc)
    {
        $this->setParameter('bank', $bc);
    }

    function getResultMode()
    {
        return $this->getParameter('result_mode') ? : '0';
    }

    function setResultMode($mode)
    {
        $this->setParameter('result_mode', $mode);
    }

    function getDescription(){
        return $this->getParameter('description') ? : '';
    }

    function setDescription($desc){
        $this->setParameter('description', $desc);
    }
}
