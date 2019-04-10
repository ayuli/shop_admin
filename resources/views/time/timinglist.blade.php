<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="/css/css.css" />
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <title>Document</title>
    <style>
        a{border:1px solid #666;text-decoration: none;padding: 0 5px;border-radius: 5px;color:#666;}
        a:hover,a.current{border:1px solid plum;color:plum;}
    </style>
</head>

<div class="add">
    <a class="addA" href="javascript:;" name="start" style="background-color: white;color: plum;">开始&nbsp;&nbsp;+</a>
    <a class="addA" href="javascript:;" name="end" style="background-color: white;color: plum;">停止&nbsp;&nbsp;+</a>
    <a class="addA" href="javascript:;" name="del" style="background-color: white;color: plum;">清除&nbsp;&nbsp;+</a>
</div>

    <div id="pageAll">
        <div class="page">
            <!-- banner页面样式 -->
            <div class="connoisseur">
                        <!-- banner 表格 显示 -->
                <div class="conShow">
                    <table border="1" cellspacing="0" cellpadding="0">
                        <tr id="zuij">
                            <td width="100px" class="tdColor tdC">序号</td>
                            <td width="320px" class="tdColor">商品名称</td>
                            <td width="160px" class="tdColor">商品价格</td>
                            <td width="260px" class="tdColor">商品数量</td>
                        </tr>

                       <tbody id="goodslist">

                       </tbody>

                    </table>
                </div>
                        <!-- banner 表格 显示 end-->
            </div>
                <!-- banner页面样式end -->
        </div>
    </div>

</body>
</html>
<script>

    num = '';
    page = 0;
    $("a[name=start]").click(function () {
        clearInterval(num)
        var start = setInterval(function () {
            page++;
            $.ajax({
                url : '/setinterval/show',
                data: {page:page},
                type : 'post',
                // dataType : 'json',
                success : function (d) {
                    $("#goodslist").append(d);
                }
            })
        },2000)

        num = start
    })

    $("a[name=end]").click(function () {
        clearInterval(num)
    })
    $("a[name=del]").click(function () {
        location.href="/setinterval"
    })
</script>