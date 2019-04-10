<?php

namespace App\Http\Controllers\timing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\GoodsModel;

class timingController extends Controller
{
    /**
     * 定时器展示
     */
    public function setList()
    {
        return view('time.timinglist');
    }

    public function setShow(Request $request)
    {
        $page = $request->input('page');
        $page_num = 5;
        $start = ($page-1)*$page_num;
//        $end =  $start+$page_num-1;
        $data = GoodsModel::offset($start)->limit($page_num)->get();
        $view = view('time.goodsview',['data'=>$data]);
        $content = response($view)->getContent();
        return $content;
    }
}
