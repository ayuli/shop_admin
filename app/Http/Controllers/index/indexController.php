<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Common;
use App\model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\UserdModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;


class indexController extends Common
{
    //登陆
    public function login()
    {
        $redirect_uri = urlencode("https://www.xiaomeinan.com/redirecturi");
        $scope = "snsapi_userinfo";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=STATE#wechat_redirect";
        return view('index.login',['url'=>$url]);
    }

    public function loginDo(Request $request)
    {
        $phone = $request->input('phone');
        $pwd = $request->input('pwd');
        $phone_res = UserdModel::where(['phone'=>$phone,'pwd'=>$pwd])->first();
        $openid = $phone_res['openid'];

        if($phone_res){
            // 存cookie
            Cookie::queue('user_id',$phone_res['id']);

            //发送登陆成功模板
            $this->sendModel($openid);


            if(empty($phone_res['openid'])){
                $data = [
                    'error'=>0,
                    'msg' => '登陆成功,未绑定公众号'
                ];
            }else{
                $data = [
                    'error'=>0,
                    'msg' => '登陆成功'
                ];
            }

        }else{
            $data = [
                'error'=>1001,
                'msg' => '登陆失败,没有该用户或者密码错误'
            ];
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);

    }

    /**
     * 发送登陆成功模板
     */
    public function sendModel($openid)
    {
        $send_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->accessToken();
        $send_data = [
            "touser"=>"$openid",
            "template_id"=>"-5PQkgIt_EQb_V-G0nEfHb8Kg7wWPQR73sEGu9DSKuU",
            "url"=>"http://weixin.qq.com/download",
            "data"=>[

            ]
        ];
        $send_json = json_encode($send_data,JSON_UNESCAPED_UNICODE);

        $obj = new \Url();
        $obj->sendPost($send_url,$send_json);
    }

    /**
     * 个人中心
     */
    public function userCenter()
    {
        return view('index.usercenter');
    }

    /**
     * 首页
     */
    public function index()
    {
        return view('index.index');
    }

    /**
     * 商品详情
     * openid user_id goods_id time
     */
    public function part(Request $request)
    {

        $user_id = $request->cookie('user_id');
        $user = UserdModel::where(['id'=>$user_id])->first();
        $openid = $user->openid;
        $time = date('Y-m-d H:i:s',time());
        $id = Redis::incr('id');
        $hash_key = 'record_'.$id;
//        Redis::hset($hash_key,'id',$id);
        Redis::hset($hash_key,'goods_id',1);
        Redis::hset($hash_key,'user_id',$user_id);
        Redis::hset($hash_key,'openid',$openid);
        Redis::hset($hash_key,'time',$time);

        $list_key = 'list_record';
        Redis::rpush($list_key,$hash_key);

        return view('index.part');
    }

    //存redis

    /**
     * 微信注册
     */
    public function register(Request $request)
    {
    }


    /**
     * 注册下一步
     */
    public function registerDo(Request $request)
    {
        $phone = $request->input('phone');
        $openid = $request->input('openid');

        $phone_res = UserdModel::where(['phone'=>$phone])->first();
        if($phone_res){
            $p_user = UserdModel::where(['phone'=>$phone])->update(['openid'=>$openid]);
        }else{
            //获取用户基本信息
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->accessToken()."&openid=$openid&lang=zh_CN";

            $obj = new \Url();
            $json = $obj->sendGet($url);
            $user_arr = json_decode($json,true);
//            var_dump($user_arr);die;
            $nickname = $user_arr['nickname'];
            $data = [
                'name'=>$nickname,
                'phone'=>$phone,
                'openid'=>$openid
            ];
            UserdModel::insert($data);
        }

        $arr = [
            'error'=>0,
            'msg'=>'绑定成功'
        ];
//        if($p_user){
        return  json_encode($arr);
//            return view('index.usercenter',['data'=>$p_user]);
//        }
    }

    /**
     * 前台用户管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userShow()
    {
        $list_key = "red_list";
        //展示
        $list = Redis::lrange($list_key,0,-1);
        $user_arr = [];
        foreach ($list as $v){
            $arr = Redis::hgetall($v);
            array_push($user_arr,$arr);
        }
//        var_dump($user_arr);die;
        return view('index.usershow',['arr'=>$user_arr]);
    }

    /**
     *缓存进数据库
     */
    public function userCache()
    {
        $list_key = "red_list";
        //展示
        $list = Redis::lrange($list_key,0,-1);
        $user_arr = [];
        foreach ($list as $v){
            $arr = Redis::hgetall($v);
            $dat = [
                'name'=>$arr['name'],
                'openid'=>$arr['openid']
            ];
            array_push($user_arr,$dat);
        }

        UserdModel::insert($user_arr);

        Redis::flushall();
//        var_dump($user_arr);

    }


    /**
     * 前台退出
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quit(Request $request){
        $redirect_uri = urlencode("https://www.xiaomeinan.com/redirecturi");
        $scope = "snsapi_userinfo";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=STATE#wechat_redirect";
        return view('index.login',['url'=>$url]);
    }


    /**
     * 个人中心
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sesssion(Request $request)
    {
        $phone = $request->input('phone');
        $p_user = UserdModel::where(['phone'=>$phone])->first();

        return view('index.usercenter',['data'=>$p_user]);

    }






}
