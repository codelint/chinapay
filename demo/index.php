<?php
$out_trade_no = time() . rand(1000, 9000);
?>
<!DOCTYPE html>
<html>
<head>
    <title>支付网关测试</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        select, input: {
            font-size: 16px;
            width: 140px;
        }
    </style>
</head>
<body style="margin: auto 0; text-align: center">
<div><h2>支付宝支付测试</h2></div>
<div style="font-size: 18px">
    <form action="alipay.redirect.php">
        <label for="gateway-select">网关
            <select id="gateway-select" name="gateway">
                <option value="Alipay_WapExpress">手机.支付宝</option>
            </select>
        </label>
        <br><br>
        <label for="out_trade_no-input">
            订单号
            <input id="out_trade_no-input" name="out_trade_no" value="<?php echo $out_trade_no; ?>">
        </label>
        <br><br>
        <label for="total_fee-input">
            金额
            <input id="total_fee-input" type="number" name="total_fee" value="0.01">
        </label>

        <br><br>
        <label for="subject-input">
            支付提示语
            <input id="subject-input" name="subject" type="text" value="你正在为订单[<?php echo $out_trade_no; ?>]支付...">
        </label>
        <br><br>
        <button>提交</button>
    </form>
</div>

<div><h2>微信支付测试</h2></div>
<div style="font-size: 18px">
    <form action="wechat.redirect.php">
        <label for="gateway-select">网关
            <select id="gateway-select" name="gateway">
                <option value="Wechat_Express">微信</option>
            </select>
        </label>
        <br><br>
        <label for="out_trade_no-input">
            订单号
            <input id="out_trade_no-input" name="out_trade_no" value="<?php echo $out_trade_no; ?>">
        </label>
        <br><br>
        <label for="total_fee-input">
            金额
            <input id="total_fee-input" type="text" name="total_fee" value="0.01">
        </label>

        <br><br>
        <label for="subject-input">
            支付提示语
            <input id="subject-input" name="subject" type="text" value="你正在为订单[<?php echo $out_trade_no; ?>]支付...">
        </label>
        <br><br>
        <label for="subject-input">
            支付类型
            <select id="subject-input" name="pay_type">
                <option value="JSAPI">网页支付</option>
                <option value="APP">APP_Prepay</option>
            </select>
        </label>
        <br><br>
        <button>提交</button>
    </form>
</div>


<div><h2>微信退款测试</h2></div>
<div style="font-size: 18px">
    <form action="wechat.refund.php">
        <label for="gateway-select">网关
            <select id="gateway-select" name="gateway">
                <option value="Wechat_Express">微信</option>
            </select>
        </label>
        <br><br>
        <label for="refund_out_refund_no-input">
            商户退款单号
            <input id="refund_out_refund_no-input" name="out_refund_no" value="<?php echo $out_trade_no; ?>">
        </label>
        <br><br>
        <label for="refund_out_trade_no-input">
            商户订单号
            <input id="refund_out_trade_no-input" name="out_trade_no" value="MP100000065">
        </label>
        <br><br>
        <label for="transaction_id-input">
            微信订单号
            <input id="transaction_id-input" name="transaction_id" value="">
        </label>
        <br><br>
        <label for="refund_total_fee-input">
            订单金额
            <input id="refund_total_fee-input" type="text" name="total_fee" value="0.01">
        </label>
        <br><br>
        <label for="refund_fee-input">
            退款金额
            <input id="refund_fee-input" type="text" name="refund_fee" value="0.01">
        </label>
        <br><br>
        <button>提交</button>
    </form>
</div>

<div><h2>百度支付测试</h2></div>
<div style="font-size: 18px">
    <form action="baifu.redirect.php">
        <label for="gateway-select">网关
            <select id="gateway-select" name="gateway">
                <option value="Wechat_Express">百度</option>
            </select>
        </label>
        <br><br>
        <label for="out_trade_no-input">
            订单号
            <input id="out_trade_no-input" name="out_trade_no" value="<?php echo $out_trade_no; ?>">
        </label>
        <br><br>
        <label for="total_fee-input">
            金额
            <input id="total_fee-input" type="text" name="total_fee" value="0.01">
        </label>

        <br><br>
        <label for="subject-input">
            支付提示语
            <input id="subject-input" name="subject" type="text" value="你正在为订单[<?php echo $out_trade_no; ?>]支付...">
        </label>
        <br><br>
        <!--        <label for="subject-input">-->
        <!--            支付类型-->
        <!--            <select id="subject-input" name="pay_type">-->
        <!--                <option value="JSAPI">网页支付</option>-->
        <!--                <option value="APP">APP_Prepay</option>-->
        <!--            </select>-->
        <!--        </label>-->
        <!--        <br><br>-->
        <button>提交</button>
    </form>
</div>

</body>
</html>
