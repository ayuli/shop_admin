@foreach($data as $v)
<tr>
    <td>{{$v->goods_id}}</td>
    <td>{{$v->goods_name}}</td>
    <td>{{$v->price}}</td>
    <td>{{$v->store}}</td>
</tr>
@endforeach