<?php
require_once('./config.php');

$out_trade_no = empty($_GET['out_trade_no']) ? '' : $_GET['out_trade_no'];

$cache = get_cache();

// header("Content-type: application/json");
$data = array(
    $out_trade_no => $cache->fetch($out_trade_no),
    'last_notify' => $cache->fetch(LAST_NOTIFY_CACHE_KEY),
    'last_error' => $cache->fetch(LAST_ERROR_CACHE_KEY)
);
$serial = $cache->fetch($out_trade_no);
$serial['trade_no'] = $out_trade_no;
$last_notify = $cache->fetch(LAST_NOTIFY_CACHE_KEY);
$last_error = $cache->fetch(LAST_ERROR_CACHE_KEY);
// echo(json_encode($data));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title><?php echo $_GET['out_trade_no'] ?></title>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/json_to_table.js"></script>
</head>
<body>
<div style="margin: auto 0;text-align: center">
    <textarea rows="100" cols="120" style="font-size: 16px;width: 100%;margin: 0;padding: 0;border: none"
              disabled="disabled">

    </textarea>
</div>

</body>

<script type="text/javascript">
    var data = <?php echo(json_encode($data)) ?>;
    var serial = <?php echo(json_encode($serial)) ?>;
    var last_notify = <?php echo(json_encode($last_notify)) ?>;
    var last_error = <?php echo(json_encode($last_error)) ?>;
    jQuery(function($){
//        $('body').text(JSON.stringify(data));
        var str = JSON.stringify(data, '    ', 8);
        $('textarea').text(str);
//        $('body').text(ConvertJsonToTable(last_notify, 'content-table', 'table'))
    });

</script>

</html>