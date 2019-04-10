<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table border="1">

        <tr>
            <td>id</td>
            <td>openid</td>
            <td>add_time</td>
            <td>local_file_name</td>
            <td>msg_id</td>
        </tr>
@foreach($arr as $v)
        <tr>
            <td>{{$v['id']}}</td>
            <td>{{$v['openid']}}</td>
            <td>{{$v['add_time']}}</td>
            <td><img src="./logs/{{$v['local_file_name']}}" alt="" width="100px" height="100px"></td>
            <td>{{$v['msg_id']}}</td>
        </tr>
@endforeach

    </table>

    {{ $arr->links() }}

</body>
</html>