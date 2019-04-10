<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>行家-有点</title>
    <link rel="stylesheet" type="text/css" href="/css/css.css" />
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="js/page.js" ></script> -->
</head>

<body>
<div id="pageAll">


    <div class="page">
        <!-- banner页面样式 -->
        <div class="connoisseur">

            <!-- banner 表格 显示 -->
            <div class="conShow">
                <table border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="100px" class="tdColor tdC">序号</td>
                        <td width="300px" class="tdColor">订单号</td>
                        <td width="140px" class="tdColor">商品名称</td>
                        <td width="200px" class="tdColor">支付状态</td>
                        <td width="200px" class="tdColor">支付平台</td>
                        <td width="200px" class="tdColor">支付时间</td>
                    </tr>
                    @foreach($data as $k=>$v)
                        <tr>
                            <td>{{$v['order_id']}}</td>

                            <td>{{$v['order_sn']}}</td>
                            <td>{{$v['goods_id']}}</td>
                            <td>
                                @if($v['is_pay']==1)
                                未支付
                                @elseif($v['is_pay']==2)
                                已支付
                                @elseif($v['is_pay']==3)
                                已退款
                                @endif
                            </td>
                            <td>
                                @if($v['plat']==1)
                                    支付宝
                                @elseif($v['plat']==2)
                                    微信
                                @endif
                            </td>
                            <td>{{date('Y-m-d H:i:s',$v['pay_time'])}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <!-- banner 表格 显示 end-->
        </div>
        <!-- banner页面样式end -->
    </div>

</div>


</body>

<script type="text/javascript">
</script>
</html>