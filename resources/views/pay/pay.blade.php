<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微信支付二维码</title>
</head>
<body>
    <div id="canvas" style="">

    </div>
</body>
</html>
<script src="./js/jquery.min.js"></script>
<script src="./js/qrcode.min.js"></script>
<script>
    var qrcode = new QRCode('canvas', {
        text: "{{$url}}",
        width: 300,
        height: 300,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
    });

</script>