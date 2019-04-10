<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Apptest extends Controller
{
    //
    public function test(Request $request)
    {
        $name = $request->input('name');
        $pwd = $request->input('pwd');
        if($name=='y'){
            if($pwd==1){
                $json = [
                   'error' => 0,
                   'msg' => "登陆成功!"
                ];
                return json_encode($json);
                exit;
            }
        }
        $json = [
            'error' => 101,
            'msg' => "输入有误!"
        ];
        return json_encode($json);
    }
}
