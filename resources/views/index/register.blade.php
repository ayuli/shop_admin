<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>注册</title>
    <meta content="app-id=984819816" name="apple-itunes-app" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no, maximum-scale=1.0" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <link href="/index/css/comm.css" rel="stylesheet" type="text/css" />
    <link href="/index/css/login.css" rel="stylesheet" type="text/css" />
    <link href="/index/css/vccode.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/index/layui/css/layui.css">
    <script src="/index/js/jquery-1.11.2.min.js"></script>
</head>
<body>

<!--触屏版内页头部-->
<div class="m-block-header" id="div-header">
    <strong id="m-title">注册</strong>
    <a href="javascript:history.back();" class="m-back-arrow"><i class="m-public-icon"></i></a>
    <a href="/" class="m-index-icon"><i class="m-public-icon"></i></a>
</div>
<div class="wrapper">
    <input name="hidForward" type="hidden" id="hidForward" />
    <div class="registerCon">
        <ul>
            <li class="accAndPwd">
                <dl>
                    <input type="hidden" value="{{$openid}}" id="openid">
                    <s class="phone"></s><input id="phone" maxlength="11" type="number" placeholder="请输入您的手机号码" value="" />
                    <span class="clear">手机号</span>
                </dl>

            </li>
            <li><a id="btnNext" href="javascript:;" class="orangeBtn loginBtn">下一步</a></li>
        </ul>
    </div>


    <div class="footer clearfix" style="display:none;">
        <ul>
            <li class="f_home"><a href="/v44/index.do" ><i></i>云购</a></li>
            <li class="f_announced"><a href="/v44/lottery/" ><i></i>最新揭晓</a></li>
            <li class="f_single"><a href="/v44/post/index.do" ><i></i>晒单</a></li>
            <li class="f_car"><a id="btnCart" href="/v44/mycart/index.do" ><i></i>购物车</a></li>
            <li class="f_personal"><a href="/v44/member/index.do" ><i></i>我的云购</a></li>
        </ul>
    </div>
    <div class="layui-layer-move"></div>

    <script src="/index/layui/layui.js"></script>


    <script>
        // 下一步提交
        $('#btnNext').click(function(){
            var phone = $("#phone").val();
            var openid = $("#openid").val();

            $.ajax({
                url     :   '/index/registerdo',
                type    :   'post',
                data    :   {phone:phone,openid:openid},
                dataType:   'json',
                success :   function(d){
                    if(d.error==0){
                        alert(d.msg)
                        location.href="/sesssion?phone="+phone
                    }
                }
            })
        })
    </script>
    <script src="/index/js/all.js"></script>
</body>
</html>
