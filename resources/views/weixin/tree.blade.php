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
                <form>
                    <div class="cfD">
                        工作年限：<select>
                            <option>1年以内</option>
                        </select> 审核状态：<label>
                            <input type="radio" checked="checked" name="styleshoice1" />
                            &nbsp;未审核</label>
                        <label>
                            <input type="radio" name="styleshoice1" />&nbsp;已通过</label>
                        <label class="lar">
                            <input type="radio" name="styleshoice1" />&nbsp;不通过</label>
                        推荐状态：<label>
                            <input type="radio" checked="checked" name="styleshoice2" />&nbsp;是</label>
                        <label>
                            <input type="radio" name="styleshoice2" />&nbsp;否</label>
                    </div>
                    <div class="cfD">
                        <input class="addUser" type="text" placeholder="输入用户名/ID/手机号/城市" />
                        <button class="button">搜索</button>
                        <a class="addA addA1" href="connoisseuradd.html">添加行家+</a>
                    </div>
                </form>
            </div>
            <!-- banner 表格 显示 -->
            <div class="conShow">
                <table border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="100px" class="tdColor tdC">序号</td>
                        <td width="300px" class="tdColor">openid</td>
                        <td width="140px" class="tdColor">类型</td>
                        <td width="200px" class="tdColor">扫码时间</td>
                    </tr>
                    @foreach($arr as $k=>$v)
                        <tr>
                            <td>{{$v['id']}}</td>

                            <td>{{$v['openid']}}</td>
                            @if($v['type']=='1001')
                                <td>金融</td>
                            @elseif($v['type']=='1002')
                                <td>教育</td>
                            @elseif($v['type']=='1003')
                                <td>房地产</td>
                            @endif
                            <td>{{date("Y-m-d H:i:s",$v['createtime'])}}</td>

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
</html>