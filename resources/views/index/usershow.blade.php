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
            <div class="conform">
                <h1>微信用户</h1>
            </div>
            <!-- banner 表格 显示 -->
            <br class="conShow">
                <table border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="66px" class="tdColor tdC">序号</td>
                        <td width="170px" class="tdColor">用户名</td>
                        <td width="180px" class="tdColor">openid</td>
                        <td width="175px" class="tdColor">添加时间</td>
                    </tr>
                    @foreach($arr as $k=>$v)
                    <tr>
                        <td>{{$v['id']}}</td>
                        <td>{{$v['name']}}</td>
                        <td>{{$v['openid']}}</td>
                        <td>{{date('Y-m-d H:i:s',$v['time'])}}</td>
                    </tr>
                    @endforeach
                </table>


            </div>
            <!-- banner 表格 显示 end-->
        </div>
        <!-- banner页面样式end -->
</div>




</body>

<script type="text/javascript">
    //加入黑名单

    $(".blacklist").click(function () {
        //g获取所有复选框
        var checkboxAll = $("[type='checkbox']");

        var openid = '';
        checkboxAll.each(function(){
            if($(this).prop('checked')==true){
                openid += $(this).attr('openid')+','
            }
        });
        // console.log(openid)
        $.ajax({
            url     :   '/blacklist',
            type    :   'post',
            data    :   {openid:openid},
            dataType:   'json',
            success :   function(d){
                if(d.errcode==0){
                    alert("以加入黑名单");
                }else{
                    alert("操作有误");
                }
            }
        });

    })

    //批量加入标签

    $(".jointags").click(function () {
        //获取所有复选框
        var checkboxAll = $("[type='checkbox']");
        var openid = '';
        // 获取下拉菜单
        var tagid = $(".tagid").val();
        // console.log(tagid)

        checkboxAll.each(function(){

            if($(this).prop('checked')==true){
                openid += $(this).attr('openid')+','
            }

        });
        // console.log(openid)
        $.ajax({
            url     :   '/joinTags',
            type    :   'post',
            data    :   {openid:openid,tagid:tagid},
            dataType:   'json',
            success :   function(d){
                if(d.errcode==0){
                    alert("已将用户批量加入标签");
                }else{
                    alert("操作有误");
                }
            }
        });

    })


</script>
</html>