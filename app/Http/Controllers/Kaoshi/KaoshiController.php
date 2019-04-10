<?php

namespace App\Http\Controllers\Kaoshi;

use App\Http\Controllers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\MediaModel;

class KaoshiController extends Common
{
//    自定义菜单样式
    public function definedMenusList()
    {
        return view('kaoshi.kaoshi');
    }


    //死的自定义菜单执行
    public function definedMenusExe()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->accessToken();

        $data = [
            "button"=>[
                [
                    "type"=>"click",
                    "name"=>"今日歌曲",
                    "key"=>"V1001_TODAY_MUSIC"
                ],
                [
                    "name"=>"菜单",
                    "sub_button"=>[
                        [
                            "type"=>"view",
                            "name"=>"搜索",
                            "url"=>"http://www.soso.com/"
                        ],
                        [
                            "type"=>"miniprogram",
                            "name"=>"wxa",
                            "url"=>"http://mp.weixin.qq.com",
                            "appid"=>"wx286b93c14bbf93aa",
                            "pagepath"=>"pages/lunar/index"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"赞一下我们",
                            "key"=>"V1001_GOOD"
                        ]]
                ],
                [
                    "type"=> "pic_weixin",
                    "name"=> "微信相册发图",
                    "key"=> "rselfmenu_1_2",
                    "sub_button"=> [ ]
                ]
            ]
        ];

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $obj = new \Url();
        $send = $obj->sendPost($url,$json);
        var_dump($send);

    }

    //临时素材展示
    public function definedMenusShow(Request $request)
    {
        $arr = MediaModel::simplePaginate(3);
        return view('kaoshi.list',['arr'=>$arr]);
    }

    public function test()
    {
        $en = encrypt1('111','111');
        echo $en;
//        echo "</br>";
//        echo decrypt1($en,'111');
    }


}
