<?php 
namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\model\UserdModel;

use App\model\OrderModel;
use App\model\RecordMoel;
use App\model\MsgModel;
use App\model\GoodsModel;
use App\model\MediaModel;




class weixinContorller extends Common
{
    // 微信xml
    public function xml()
    {
//        echo $_GET['echostr'];die;
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents("./logs/weixin.log", $log_str, FILE_APPEND);

        $xml = simplexml_load_string($data);//将xml字符串转换成对象

        $ToUserName = $xml->ToUserName;
        $openid = $xml->FromUserName;
        $MsgType = $xml->MsgType;

        $Event = $xml->Event;  //二维码类型  SCAN
        $EventKey = $xml->EventKey;    //二维码type

        if($MsgType=='event'){


            if($Event=='SCAN'){ //二维码

                //二维码存缓存
                //set集合
                $setkey = "set_qr_".$EventKey;
                $set_openid = Redis::sadd($setkey,"$openid");

                if($set_openid){
                    //哈希
                    $id = Redis::incr('id');
                    $hash_qr = 'hash_qr_'.$id;

                    $time = intval(time());
                    Redis::hset($hash_qr,'id',$id);
                    Redis::hset($hash_qr,'openid',"$openid");
                    Redis::hset($hash_qr,'type',"$EventKey");
                    Redis::hset($hash_qr,'createtime',$time);
                    //队列
                    $lish_qr = "lish_qr";
                    Redis::rpush($lish_qr,$hash_qr);

                }



            }
            //扫码关注事件
            if($Event=='subscribe') {
                $xml = "<xml><ToUserName><![CDATA[".$openid."]]></ToUserName>
                        <FromUserName><![CDATA[".$ToUserName."]]></FromUserName>
                        <CreateTime>1553510519</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA["."欢迎关注熊熊!"."]]></Content>
                        <MsgId>22240921925165938</MsgId>
                        </xml>";
                return $xml;
            }


        }else if($MsgType=='text'){   // 消息事件

            $openid = $xml->FromUserName;
            $content = $xml->Content;

            $id = Redis::incr('id');

            //哈希 hash
            $hash_key = "climsg_".$id;
            Redis::hset($hash_key,'id',$id);
            Redis::hset($hash_key,'openid',"$openid");
            Redis::hset($hash_key,'msg',"$content");
            Redis::hset($hash_key,'status',1); //1是未读 2是已读
            //队列 list
            $list_key = 'list_climsg';
            Redis::rpush($list_key,$hash_key);



            $data = [
                'openid' =>"$openid",
                'msg'=>"$content"
            ];
            MsgModel::insert($data);


        }else if($MsgType=='image') {   // 消息事件

            $PicUrl = $xml->PicUrl;
            $MsgId = $xml->MsgId;
            $MediaId = $xml->MediaId;

            //先获取临时素材
            $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->accessToken()."&media_id=$MediaId";
            $obj = new \Url();
            $send = $obj->sendGet($url);
            $name = rand(100,999).date('YmdHi').rand(100,999).".jpg";
//            file_put_contents("./logs/linsu.log",$send,FILE_APPEND);
            file_put_contents("./logs/".$name,$send);

            //存到数据库
            $data = [
                'openid'    => $openid,
                'add_time'  => time(),
                'msg_type'  => 'image',
                'media_id'  => $MediaId,
                'msg_id'    => $MsgId,
                'local_file_name'   => $name
            ];

            MediaModel::insert($data);



            $str = 'PicUrl:'.$PicUrl."\n".'MsgId:'.$MsgId."\n".'MediaId:'.$MediaId;
            $log = date('Y-m-d H:i:s') . "\n" . $str . "\n<<<<<<<";
            file_put_contents('./logs/kaishi.log',$log,FILE_APPEND);

        }

    }




    /**
     * 二维码用户展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCodeShow()
    {
        $lish_qr = "lish_qr";
        $list = Redis::lrange($lish_qr,0,-1);
        $arr = [];
        foreach($list as $v){
            $data = Redis::hgetall($v);
            array_push($arr,$data);
        }
        return view('weixin.tree',['arr'=>$arr]);
    }
    /**
     * 添加标签
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tagsAdd()
    {
        return view('tags.add');
    }

    /**
     * 执行添加标签
     */
    public function tagsAdo()
    {
        $tagsName = $_POST['tags_name'];
        if (empty($tagsName)) {
            exit("2");
        }
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=" . $this->accessToken();

        $arrTage = [
            'tag' => [
                'name' => $tagsName
            ]
        ];
        $json = json_encode($arrTage, JSON_UNESCAPED_UNICODE);

        $obj = new \Url();
        $bol = $obj->sendPost($url, $json);
        if ($bol) {
            echo '1';
        }

    }

    /**
     * 展示标签
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tagsShow()
    {
        $obj = new \Url();

        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=" . $this->accessToken();
        $tag_obj = $obj->sendGet($url);
        $tag_arr = json_decode($tag_obj, true);

        $data = [
            'arr' => $tag_arr['tags'],
        ];
        return view('tags.show', $data);
    }

    /**
     * 删除标签
     */
    public function tagsDel(Request $request)
    {
        $id = $request->input('id');
//        echo'<pre>';print_r($id);echo '<pre>';exit;
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=" . $this->accessToken();
        $data = [
            "tag" => [
                "id" => $id
            ]
        ];
        $json = json_encode($data);
        $obj = new \Url;
        $send = $obj->sendPost($url, $json);

    }

    /**
     * 修改标签
     */
    public function tagsUpdate(Request $request)
    {
        $id = $request->input('id');
        $data = [
            'id' => $id
        ];
        return view('tags.update', $data);
    }

    /**
     * 修改执行
     * @param Request $request
     */
    public function tagsUpdateDo(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('tags_name');
        //POST
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=" . $this->accessToken();
        $data = [
            "tag" => [
                "id" => $id,
                "name" => $name
            ]
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $obj = new \Url;
        $send = $obj->sendPost($url, $json);
        return $send;

    }

    /**
     * 自定义菜单添加展示
     */
    public function menus()
    {
        return view('menus/menus');
    }

    /**
     * 自定义菜单添加执行
     */
    public function menusAdd(Request $request)
    {
        $data = $request->input();
        $count = count($data);
        $arr = [];
        for ($i = 0; $i < $count; $i++) {
            $arr[$i]['name'] = $data['name'][$i];
            $arr[$i]['type'] = $data['type'][$i];
            if ($data['type'][$i] == 'click') {
                $arr[$i]['key'] = $data['keyurl'][$i];
            } else {
                $arr[$i]['url'] = $data['keyurl'][$i];
            }
        }

        $info['button'] = $arr;

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->accessToken();


        $json = json_encode($info, JSON_UNESCAPED_UNICODE);

        $obj = new \Url();

        $send = $obj->sendPost($url, $json);

        return $send;

    }

    /**
     * 自定义菜单展示列表
     */
    public function menusShow()
    {
//        echo phpinfo();die;
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=" . $this->accessToken();

        $obj = new \Url();
        $send = $obj->sendGet($url);
        $json = json_decode($send, true);
//        echo'<pre>';print_r($json['menu']);echo '<pre>';die;
        return view('menus.menusshow', $json['menu']);
    }

    /**
     * 用户管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userManage()
    {
        //获取用户信息
        $json3 = $this->userInfo();

        $obj = new \Url();
        $url3 = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=" . $this->accessToken();
        $tag_obj = $obj->sendGet($url3);
        $tag_arr = json_decode($tag_obj, true);
        $json3['arr'] = $tag_arr['tags'];

//        echo'<pre>';print_r($json3);echo '<pre>';die;
        return view('user.manage', $json3);

    }

    /**
     * 批量拉黑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blacklist(Request $request)
    {
        $openid = $request->input('openid');
        $arr_openid = explode(',', rtrim($openid, ','));

        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=" . $this->accessToken();

        $d['openid_list'] = $arr_openid;
        $json = json_encode($d, JSON_UNESCAPED_UNICODE);

        $obj = new \Url;
        $send = $obj->sendPost($url, $json);

        return $send;

    }

    /**
     * 黑名名单单列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blackShow()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=" . $this->accessToken();

        $data = [
            "begin_openid" => ''
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $obj = new \Url;
        $send = $obj->sendPost($url, $json);
        $arr = json_decode($send, true);
        // 获取到了黑名单的用户openid

        if (empty($arr['data']['openid'])) {
            exit('没有被拉黑的用户');
        }

        //获取基本信息
        $url2 = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $this->accessToken();

        foreach ($arr['data']['openid'] as $k => $v) {
            $d['user_list'][] = [
                "openid" => $v,
                "lang" => "zh_CN"
            ];
        }
        $json2 = json_encode($d, JSON_UNESCAPED_UNICODE);
        $send2 = $obj->sendPost($url2, $json2);

        $json3 = json_decode($send2, true);
//        echo'<pre>';print_r($json3);echo '<pre>';die;
        return view('user.blackshow', $json3);
    }

    /**
     * 取消拉黑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function offBlack(Request $request)
    {
        $openid = $request->input('openid');
//        echo $openid;
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?access_token=" . $this->accessToken();
        $data = [
            "openid_list" => [
                $openid
            ]
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $obj = new \Url;
        $send = $obj->sendPost($url, $json);

        return redirect('/blackShow'); //重定向跳转

    }

    /**
     * 批量加入标签
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function joinTags(Request $request)
    {
        $openid = $request->input('openid');
        $arr_openid = explode(',', rtrim($openid, ','));
        $tagid = $request->input('tagid');

        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=" . $this->accessToken();
        $data['openid_list'] = $arr_openid;
        $data['tagid'] = $tagid;
//        echo'<pre>';print_r($data);echo '<pre>';die;
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $obj = new \Url;
        $send = $obj->sendPost($url, $json);
        return $send;

    }

    /**
     * 查看标签下的用户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function examine(Request $request)
    {
        $tagid = $request->input('tagid');
//        echo $tagid;die;
        $url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=" . $this->accessToken();
        $data = [
            "tagid" => $tagid,
            "next_openid" => "" //第一个拉取的OPENID，不填默认从头开始拉取
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $obj = new \Url;
        $send = $obj->sendPost($url, $json);

        $arr = json_decode($send, true);

        if (empty($arr['data']['openid'])) {
            exit('标签下没有用户');
        }
        //获取标签下的用户信息
        $url2 = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $this->accessToken();

        foreach ($arr['data']['openid'] as $k => $v) {
            $d['user_list'][] = [
                "openid" => $v,
                "lang" => "zh_CN"
            ];
        }
//        echo'<pre>';print_r($d);echo '<pre>';die;

        $json2 = json_encode($d, JSON_UNESCAPED_UNICODE);
        $send2 = $obj->sendPost($url2, $json2);

        $json3 = json_decode($send2, true);

        $json3['tagid'] = $tagid;
//        echo'<pre>';print_r($json3);echo '<pre>';die;
        return view('tags.examine', $json3);
    }

    /**
     * 批量取消用户下的标签
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function examineOff(Request $request)
    {
        $openid = $request->input('openid');
        $arr_openid = explode(',', rtrim($openid, ','));
        $tagid = $request->input('tagid');
//        echo $openid;echo $tagid;die;
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=" . $this->accessToken();

        $data['openid_list'] = $arr_openid;
        $data['tagid'] = $tagid;
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $obj = new \Url;
        $send = $obj->sendPost($url, $json);
        return $send;

    }

    /**
     * 上传临时素材
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadFile()
    {
        return view('upload.uploadfile');

    }

    /**
     * 临时素材执行
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadFileDo($filepath)
    {
//        $filePath = $request->input('path');
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $this->accessToken() . "&type=image";

        $fileSzie = filesize($filepath);

        $media = new \CURLFile($filepath);  //media;

        $data = [
            'media' => $media,
            'form-data' => [
                'filename' => time(),
                'filelength' => $fileSzie,  //文件大小
                'content-type' => 'image/jpeg',
            ]
        ];

        $obj = new \Url();
        $send = $obj->sendPost($url, $data);
        return $send;
    }

    /**
     * 临时文件展示展示展示
     */
    public function uploadShow(Request $request)
    {
        $page = $request->input('page', 1);
        $page_num = 3;
        $start = ($page - 1) * $page_num;
        $end = $start + $page_num - 1;

        $list_key = 'list_key';
        $total = ceil(Redis::llen($list_key) / $page_num); //总条数

        $lrange = Redis::lrange($list_key, $start, $end); //获取队列
        $arr = [];
        foreach ($lrange as $k => $v) {
            $data = Redis::hgetall($v);
            array_push($arr, $data);
        }
//        echo'<pre>';print_r($arr);echo '<pre>';
        //处理分页
        $prev = $page - 1 < 1 ? 1 : $page - 1;
        $next = $page + 1 > $total ? $total : $page + 1;

        $data = [
            'arr' => $arr,
            'first' => 1,
            'prev' => $prev,
            'next' => $next,
            'total' => $total
        ];
        return view('upload.uploadshow', $data);
    }

    /**
     * 无调转显示图片
     */
    public function uploadAjax(Request $request)
    {

        if ($request->isMethod('POST')) {
            $fileCharater = $request->file('file');
//            var_dump($fileCharater);die;
            if ($fileCharater->isValid()) {
                $ext = $fileCharater->getClientOriginalExtension();// 文件后缀
                $path = $fileCharater->getRealPath();//获取文件的绝对路径
                $filename = date('Ymdhis') . '.' . $ext;//定义文件名
                Storage::disk('public')->put($filename, file_get_contents($path));
                $file = "./upload/" . $filename;
                //调用接口
                $info = $this->uploadFileDo($file);
                //把调用回来的数据储存到redis缓存
                $arrImg = json_decode($info, true);
                $media_id = $arrImg['media_id'];
                $created_at = $arrImg['created_at'];
                $endTime = $created_at + 86400 * 3;
//                var_dump($endTime);die;
                $data = [
                    'media_id' => $media_id,
                    'path' => $file,
                    'created_at' => $created_at,
                    'end_time' => $endTime
                ];

                $this->uploadCach($data);
                return json_encode($file);


            }
        }


    }

    /**
     * 将临时素材存到缓存中
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadCach($data)
    {
        $id = Redis::incr('id');
        //哈希
        $hash_key = 'id_' . $id;

        Redis::hSet($hash_key, 'id', $id);
        Redis::hSet($hash_key, 'path', $data['path']);
        Redis::hSet($hash_key, 'media_id', $data['media_id']);
        Redis::hSet($hash_key, 'created_at', $data['created_at']);
        Redis::hSet($hash_key, 'end_time', $data['end_time']);
        //队列
        $list_key = 'list_key';

        Redis::rpush($list_key, $hash_key);


    }

    /**
     * 回调地址
     */
    public function redirectUri(Request $request)
    {
        $data = $request->input();
        $code = $data['code']; //获取的code

        $access_Token = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=$code&grant_type=authorization_code";
        $info = file_get_contents($access_Token);
        $arr = json_decode($info, true);

        $openid = $arr['openid']; //获取到的openid

        $p_user = UserdModel::where(['openid' => $openid])->first();

        if ($p_user) {
            //发送登陆成功模板
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



            //获取用户基本信息
            $nickname = $this->userIn($openid);
            //存缓存
            $this->redirectRedis($openid, $nickname);
            //登陆
            session('name', $p_user['name']);
            return view('index.usercenter', ['data' => $p_user]);

        } else {

            //获取用户基本信息
            $nickname = $this->userIn($openid);
            //存缓存
            $this->redirectRedis($openid, $nickname);


            $data = [
                'openid' => $openid
            ];
            return view('index.register', $data);

        }

    }

    //获取用户基本信息
    public function userIn($openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $this->accessToken() . "&openid=$openid&lang=zh_CN";

        $obj = new \Url();
        $json = $obj->sendGet($url);
        $user_arr = json_decode($json, true);
//            var_dump($user_arr);die;
        $nickname = $user_arr['nickname'];
        return $nickname;
    }


    /**
     * 第三方用户登陆存缓存
     * @param $openid
     * @param $nickname
     */
    public function redirectRedis($openid, $nickname)
    {
        //哈希
        $id = Redis::incr('id');
        $time = time();
        $hash_key = "red_id_" . $id;
        Redis::hset($hash_key, 'id', $id);
        Redis::hset($hash_key, 'name', $nickname);
        Redis::hset($hash_key, 'openid', $openid);
        Redis::hset($hash_key, 'time', $time);
        //队列
        $list_key = "red_list";
        Redis::rpush($list_key, $hash_key);

    }


    /**
     * 生成二维码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode()
    {

        $qrCodeUrl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->accessToken();
        $qrCodeData = [
            "expire_seconds"=> 86400,
            "action_name"=> "QR_SCENE",
            "action_info"=> [
                "scene"=>[
                    "scene_id"=>1003
                ]
            ]
        ];
        $data_json = json_encode($qrCodeData);
        $obj = new \Url();
        $send_json = $obj->sendPost($qrCodeUrl,$data_json);
        $strJson = json_decode($send_json,true);
        $ticket = $strJson['ticket'];
        $this->qrCodeImg($ticket);

    }
    //生成图片
    public function qrCodeImg($ticket)
    {
        $img_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $obj = new \Url();
        $send_json = $obj->sendGet($img_url);
        file_put_contents('./imgcode/qrcode3.jpg',$send_json,FILE_APPEND);

    }



    //首页
    public function index()
    {
        return view('weixin.index');
    }


    //登陆
    public function login()
    {
        return view('weixin.login');
    }

    //redis 测试
    public function testAdd()
    {
        return view('test.add');
    }

    /**
     * redis 添加
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function testAddDo(Request $request)
    {
        $data = $request->input();
        $id = Redis::incr('id');  //字符串自增

        $user_key = "user".$id;

        Redis::hSet($user_key,'id', $id);  //哈希存值
        Redis::hSet($user_key,'name', $data['name']);
        Redis::hSet($user_key,'age', $data['age']);

        $user_list_key = "user_list";
        Redis::rpush($user_list_key,$user_key);  //推入队列

        return redirect('/redis/show');

    }

    /**
     * redis展示
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function testShow(Request $request)
    {
        $user_list_key = "user_list";

        $page = $request->input('page',1); //页码
        $page_num = 3; //每页显示条数
        $start = ($page-1)*$page_num;  //开始位置 偏移量
        $end = $start+$page_num-1;      //结束位置
        $total = ceil(Redis::llen($user_list_key)/$page_num);  //总数=向上取整(总长度/每页显示条数)

        $arrInfo = Redis::lrange($user_list_key,$start,$end);  //lrange列队列 (name,开始位置，结束位置)

        $res = [];  //空数组
        foreach($arrInfo as $v){   //  $v 是hash的名字
            $data = Redis::hgetall($v);
            array_push($res,$data);  //压入数组
        }

        //处理页码
        $prev = $page-1<1?1:$page-1;
        $next = $page+1>$total?$total:$page+1;

        //页码
        $arrPage = [
            'first' =>1,
            'prev'=> $prev,
            'next'=> $next,
            'total'=> $total
        ];

        $dataInfo['data'] = $res;
        $dataInfo['arrPage'] = $arrPage;

        return view('test.show',$dataInfo);
    }

    /**
     * 订单展示
     */
    public function orderShow()
    {
        $order = OrderModel::all();
        return view('weixin.ordershow',['data'=>$order]);
    }


    /**
     * 用户浏览商品记录
     */
    public function recordList(){
        $arr = RecordMoel::all();
        return view('weixin.recordlist',['arr'=>$arr]);
    }
}

