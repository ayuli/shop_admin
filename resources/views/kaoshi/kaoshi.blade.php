<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .btn{
            width: 80px;height:22px;background-color: #1b4b72; border: none; color: #fff3d5

        }
        .btnn{
            margin-left: 782px;
        }
    </style>
</head>
<body>
    <h1>自定义菜单</h1>
    <div    style="margin-top: 80px;">
        <h3 >一级菜单:</h3>
        <form action="" method="post"></form>
        <input type="text" name="uname">
        <select name="menus" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <input type="text" name="uname">
        <select name="menus" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <input type="text" name="uname">
        <select name="menus" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <h3>二级菜单</h3>
        <input type="text" >
        <select name="" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <input type="text" >
        <select name="" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

        <input type="text" >
        <select name="" id="" style="height:22px;">
            <option value="">请选择类型</option>
            <option value="click">点击事件</option>
            <option value="view">跳转事件</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <br><br><br>

        <div class="btnn">
        <input type="button" value="提交" class="btn" >
        </div>
    </div>
</body>
</html>

<script type="text/javascript" src="/js/jquery.min.js"></script>
<script>
   $(function () {
       $(".btn").click(function () {

           $("input[name=uname]").each(function () {
               console.log($(this).val());
           })

           $("select[name=menus]").each(function () {
               console.log($(this).val());
           })



       })


   })
</script>