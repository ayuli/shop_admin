<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Common
{
    /**
     * form 表单 图片切片上传
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function testList()
    {
        return view('test.sectionlist');
    }

    public function testForm(Request $request)
    {
        $file = $request->input('file');
        var_dump($file);exit;
    }

}
