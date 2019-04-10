<?php

namespace App\Http\Controllers\msg;

use App\Http\Controllers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class serviceController extends Common
{
    /**
     * 客服聊天展示
     * rpush() 推  lpop() 拉
     */
    public function serviceList(Request $request)
    {
        $openid = $request->input('openid');

        return view('msg.servicelist',['openid'=>$openid]);
    }

    /**
     * 展示消息
     */
    public function msglist(){
        //展示消息
//        $list_key = 'list_sermsg';
//        $one = Redis::lpop($list_key);
//        if(!$one){
//            $data = redis::hgetall($one);
//        }

        $list_clikey = 'list_climsg';
        $one2 = Redis::lpop($list_clikey);  //lrange  lpop

        if($one2){
            $data2 = redis::hgetall($one2);
            echo json_encode($data2);
        }

    }

    /**
     *客服发送消息
     */
    public function serviceSendMsg(Request $request)
    {
        $text = $request->input('text');
        $openid = $request->input('openid');

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->accessToken();
        $data = [
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=> [
                "content"=>$text
            ]
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $obj = new \Url();
        $send = $obj->sendPost($url,$json);

        var_dump($send);

        $arr = json_decode($send,true);
        if($arr['errcode']==0){
            //存缓存
            $this->serviceRedis($text,$openid);


        }


    }

    /**
     * 消息存缓存
     * rpush() 推  lpop() 拉
     * 表字段  id openid msg status 1未读 2已读
     */
    public function serviceRedis($text,$openid)
    {
        $id = Redis::incr('id');

        //哈希 hash
        $hash_key = "sermsg_".$id;
        Redis::hset($hash_key,'id',$id);
        Redis::hset($hash_key,'openid',"$openid");
        Redis::hset($hash_key,'msg',"$text");
        Redis::hset($hash_key,'status',1);
        //队列 list
        $list_key = 'list_sermsg';
        Redis::rpush($list_key,$hash_key);

    }




}
