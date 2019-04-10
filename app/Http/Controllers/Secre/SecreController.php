<?php

namespace App\Http\Controllers\Secre;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\model\SecreModel;

class SecreController extends Controller
{
    /**
     *添加公钥私钥到数据库
     */
    function insertKey()
    {
        $private_key = "/tmp/openssl/rsa_private.pem";
        $pblic_key = "/tmp/openssl/rsa_public.pem";
        $privatekey = file_get_contents($private_key);
        $publickey = file_get_contents($pblic_key);

        $data = [
            'public'=>$publickey,
            'private' =>$privatekey
        ];

        $public = SecreModel::where(['public'=>$publickey])->value('public');
        if(!$public){
            SecreModel::insert($data);
            echo "密钥添加成功";
        }else{
            echo "该密钥已存在";
        }

    }

    /**
     * 私钥接口 加密
     *  get传参
     * @return string
     */
    public function privateKey(Request $request)
    {
        $content = $request->input('content');
        $secre_private = SecreModel::where(['id'=>1])->value('private');
        $privatekey = openssl_pkey_get_private($secre_private);

        $encryptData="";    //秘钥字符串 要贮存的加密码
        openssl_private_encrypt($content,$encryptData,$privatekey);
        $encryption = base64_encode($encryptData);

        return $encryption;
    }

    /**
     * 公钥接口 解密
     * @param Request $request
     * @return string
     */
    public function publicKey(Request $request)
    {
        $enc_key = $request->input('key');
        $data = base64_decode($enc_key);

        $secre_public = SecreModel::where(['id'=>1])->value('public');

        $decode='';
        openssl_public_decrypt($data,$decode,$secre_public);
        return $decode;
    }



}
