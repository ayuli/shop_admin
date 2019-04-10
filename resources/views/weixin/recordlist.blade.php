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
                        <td width="100px" class="tdColor">商品id</td>
                        <td width="100px" class="tdColor">用户id</td>
                        <td width="300px" class="tdColor">openid</td>
                        <td width="200px" class="tdColor">浏览时间</td>
                    </tr>
                    @foreach($arr as $k=>$v)
                        <tr>
                            <td>{{$v['id']}}</td>

                            <td>{{$v['goods_id']}}</td>
                            <td>{{$v['user_id']}}</td>
                            <td>{{$v['openid']}}</td>
                            <td>{{$v['time']}}</td>
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