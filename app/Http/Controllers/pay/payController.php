<?php

namespace App\Http\Controllers\pay;

use App\Http\Controllers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\OrderModel;
use App\model\GoodsModel;

class payController extends Common
{
    /**
     * 微信支付 NATIVE
     * appid: wxd5af665b240b75d4
     * mch_id: 1500086022
     *商户秘钥: 7c4a8d09ca3762af61e59520943AB26Q
     * strtoupper — 将字符串转化为大写
     */
//    public function payTest()
//    {
//        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
//        $appid = 'wxd5af665b240b75d4';
//        $mch_id = '1500086022';
//        $str = md5(time());
//        $rand = date('YmdHi',time()).rand(1000,9999);
//        $ip = $_SERVER['REMOTE_ADDR']; //获取ip地址
//        $notify_url = 'https://www.xiaomeinan.com/paynotify';  //异步回调
//        $data = [
//            'appid'     =>      $appid,
//            'mch_id'    =>      $mch_id,
//            'sign_type' =>      'MD5',
//            'nonce_str' =>      $str,
//            'body'      =>      '小小鱼的商品支付',
//            'out_trade_no'=>    $rand,
//            'total_fee' =>       1,
//            'spbill_create_ip'=> $ip,
//            'notify_url'=>      $notify_url,
//            'trade_type' =>     'NATIVE',
//        ];
//
//        //字典排序
//        ksort($data);
//        $paramsA = urldecode(http_build_query($data));
//        $params=$paramsA.'&key=7c4a8d09ca3762af61e59520943AB26Q';
//        $endStr = md5($params);
//        $data['sign'] = strtoupper($endStr);  //转化为大写
//
//        $obj = new \Url();
//        $strJson = $obj->arr2Xml($data); //转化成 xml格式
//
//        $info  = $obj->sendPost($url,$strJson); //发送
//        $objxml = simplexml_load_string($info); //将xml转化成对象
//        $url = $objxml->code_url;   //获取code
//        return view('pay.pay',['url'=>$url]);
//    }

    /**
     * 支付异步回调
     * var_export — 输出或返回一个变量的字符串表示
     */
    public function payNotify(Request $request)
    {
        $xml = file_get_contents("php://input");
        $obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        $arr = json_decode(json_encode($obj),true);  //转化成数组
//        file_put_contents("./logs/pay.log", var_export($arr,true), FILE_APPEND);
        $sign = $arr['sign'];
        unset($arr['sign']);

        $xmll = simplexml_load_string($xml);
        $order_sn = $xmll->out_trade_no;
        //验签
        $newSign = $this->checkSign($arr);

        if($xmll->result_code=='SUCCESS' && $xmll->return_code=='SUCCESS'){      //微信支付成功回调
            if ($sign == $newSign) {

                //修改订单状态
                $orderWhere = [
                    'order_sn' => intval($order_sn)
                ];

                $orderData = [
                    'pay_amount'   => 1,
                    'pay_time'      =>  time(),
                    'is_pay'        =>  2,   //1未支付  2 已支付
                    'plat'          => 2 // 平台编号 1 支付宝 2 微信
                ];

                $status  = OrderModel::where($orderWhere)->first();
                if($status['is_pay']==1){
                    //修改状态
                    OrderModel::where($orderWhere)->update($orderData);

                    //给微信发送模板
                    $order = OrderModel::where(['order_sn'=>intval($order_sn)])->first();
                    $goods = GoodsModel::where(['goods_id'=>$order['goods_id']])->first();
                    $goods_name = $goods['goods_name'];
                    $openid = $xmll->openid;
                    $cash_fee = $xmll->cash_fee;

                    $this->wxModel($openid,$cash_fee,$goods_name);

                    // 减库存

                    $order = OrderModel::where($orderWhere)->first()->toArray();
                    $goodsWhere = [
                        'goods_id' =>$order['goods_id']
                    ];

                    $goods = GoodsModel::where($goodsWhere)->first();
                    $goodsData = [
                        'store' => $goods['store']-$order['pay_num']
                    ];

                    if($goodsData<=0){
                        exit('库存不足');
                    }
                    GoodsModel::where($goodsWhere)->update($goodsData);

                }

            }
        }
    }

    /**
     *发送模板
     */
    public function wxModel($openid,$cash_fee,$goods_name)
    {
        $cash_fee = $cash_fee/100;
        $template_id = 'YciOOTHrkVyhCcHA0PDb4K6WkeKAsR6X8I2aARj2fqw';
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->accessToken();
        $openid = 'oMgNg1XwjtrCkhfUqu6ZggUJpxGY';
        $data =[
            "touser" => $openid,
            'template_id' => $template_id,
            'data' => [
                'first' => [
                    'value' => '恭喜您购买成功!',
                    "color" => "#173177"
                ],
                'second' => [
                    'value' => '商品:'.$goods_name,
                    "color" => "#173177"
                ],
                'thirdly' => [
                    'value' => '价格:'.$cash_fee.'￥',
                    "color" => "#173177"
                ],
                'fourthly' => [
                    'value' => '购买时间:'.date('Y-m-d H:i:s',time()),
                    "color" => "#173177"
                ],
                'remark' => [
                    'value' => '欢迎再次购买！',
                    "color" => "#173177"
                ],
            ],
        ];

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $obj = new \Url();
        $obj->sendPost($url,$json);

    }

    /**
     * 验签
     */
    public function checkSign($arr)
    {
        ksort($arr);
        $key = '7c4a8d09ca3762af61e59520943AB26Q';
        $params = urldecode(http_build_query($arr));
        $params.="&key=$key";
        $endStr = md5($params);
        $signInfo = strtoupper($endStr);
        return $signInfo;

    }

    /**
     * ajax 轮询
     * 看订单状态 支付成功跳转
     * @param $order_id
     * @return array
     */
    public function wx_uccess($order_id)
    {
        $order = OrderModel::where(['order_id'=>$order_id])->first();
        if($order['is_pay']==2){
            $da = [
                'error' => 0,
                'msg'   => '支付成功'
            ];
        }else{
            $da = [
                'error' => 1001,
                'msg'   => '支付失败'
            ];
        }
        return $da;

    }

    public function paySuccess($order_id)
    {
        echo $order_id;
    }

}
