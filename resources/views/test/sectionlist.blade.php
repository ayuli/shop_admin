<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <style>
        .degue {
            width:254px;
            height:20px;
            background-color: green;
        }
        .show {
            height: 20px;
            background-color: red;
            width:0px;
        }
    </style>
</head>
<body>
    <div style="margin-left: 540px;margin-top: 160px;">

        <form id="myForm" action="" method="post" enctype="multipart/form-data">
            <h2>图片上传</h2>
            </br>
            <input type="file" id="img" name="imgage">
            </br><br>
            <div class="degue">
                <div class="show"></div>
                <span class="text"></span>
            </div>
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" value="上传" id="btn">
        </form>

    </div>
</body>
</html>
<script>
    $(function () {

        section_size = 1024 * 10;  //需要切片的大小
        index = 1;              //开始位置
        total_page = 0;    //切片的数量
        per = 0;            // 进度条的百分比 后面需要

        $("#btn").click(function(){ //点击上传图片
            upload(index);
        });


        function upload(index){  //切片
            var img = document.getElementById('img').files[0];
            var img_name = img.name;   //图片名字
            var img_size = img.size; // 图片大小  字节单位

            total_page = Math.ceil(img_size/section_size);  //总切割数
            var start = (index-1) * section_size;//开始位置
            var end = start+section_size;//结束位置

            per =((start/img_size)*100).toFixed(2);

            var chunk = img.slice(start,end);// 切割每页的大小数据 slice切片



            // var form = document.getElementById("myForm");
            // var form_data = new FormData(form);//表单对象
            // var name = form_data.get("imgage"); // 获取名字
            // form_data.append("file",chunk,img_name);


            var form = new FormData();//表单对象
            form.append("file",chunk,img_name);


            $.ajax({
                type:"post",
                data: form,
                url : "/sectionfrom",
                processData: false,
                contentType: false,//mima类型
                cache:false,
                dataType : "json",
                async:true,//同步
                success:function(msg){
                    if(index < totalPage){
                        index++;
                        per = per+"%";
                        $(".show").css({width:per});
                        $(".text").text(per);
                        upload(index);
                    }else{
                        $(".show").css({width:"100%"});
                        $(".text").text("100%");
                    }
                }
            });





        }
    })
</script>