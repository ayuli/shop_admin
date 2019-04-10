<?php

namespace App\Http\Controllers\indexorder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class orderController extends Controller
{
    /**
     * 订单展示
     */
    public function orderShow()
    {
        return view('indexorder.ordershow');
    }











    public function addOrderAddress(Request $request){                               //添加订单地址表

        $u_id = session("id");

        $order_id = $request->input("order_id");

        $where = [
            "u_id" =>$u_id,
            "default" =>1
        ];

        $addressInfo = User_address::where( $where )->first();

        $received_address = $addressInfo->address . $addressInfo->sign_building;

        $arr = [
            "order_id"=>$order_id,
            "user_id" =>$u_id,
            "order_receive_name" =>$addressInfo->consignee,
            "receive_tel" =>$addressInfo->tel,
            "province_id" =>1,
            "city_id" =>3,
            "area_id"=>88,
            "receive_address"=>$received_address,
        ];

        $res = Order_address::insert( $arr );

        if($res){
            return ["code"=>4,"msg"=>"添加成功"];
        }else{
            return ["code"=>5,"msg"=>"添加失败"];
        }

    }


    public function checkLog( Request $request){

        $u_id = session("id");

        if( !$u_id ){

            return  ["code"=>2,"msg"=>"未登录"];
        }
    }



}
