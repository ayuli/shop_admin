<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    echo phpinfo();exit;
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//微信 xml
Route::any('/test', 'weixin\weixinContorller@xml');


//redis 测试
Route::get('/redis/add','weixin\weixinContorller@testAdd');
Route::post('/redis/addo','weixin\weixinContorller@testAddDo');
Route::get('/redis/show','weixin\weixinContorller@testShow');//展示




//微信后台管理
Route::get('/head','weixin\moContorller@head');
Route::get('/left','weixin\moContorller@left');
Route::get('/foot','weixin\moContorller@foot');
Route::get('/main','weixin\moContorller@main');
//标签管理
Route::get('/tags/add','weixin\weixinContorller@tagsAdd');  //创建
Route::post('/tags/addo','weixin\weixinContorller@tagsAdo'); //执行添加
Route::get('/tags/show','weixin\weixinContorller@tagsShow'); //展示
Route::post('/tags/tagsDel','weixin\weixinContorller@tagsDel'); //删除
Route::get('/tags/tagsUpdate','weixin\weixinContorller@tagsUpdate'); //修改
Route::post('/tags/tagsUpdateDo','weixin\weixinContorller@tagsUpdateDo'); //修改执行


//用户管理
Route::any('/userManage','weixin\weixinContorller@userManage'); //修改


//自定义菜单
Route::get('/menus','weixin\weixinContorller@menus'); //添加展示
Route::post('/menus/add','weixin\weixinContorller@menusAdd'); //创建
Route::get('/menus/show','weixin\weixinContorller@menusShow'); //展示列表

//批量拉黑
Route::post('/blacklist','weixin\weixinContorller@blacklist'); //拉黑
Route::get('/blackShow','weixin\weixinContorller@blackShow'); //拉黑列表
Route::get('/offBlack','weixin\weixinContorller@offBlack'); //取消拉黑

Route::post('/joinTags','weixin\weixinContorller@joinTags'); //批量加入标签
Route::get('/examine','weixin\weixinContorller@examine'); //查看标签下的用户
Route::post('/examineOff','weixin\weixinContorller@examineOff'); //批量取消标签下的用户

// 上传临时素材
Route::get('/uploadfile','weixin\weixinContorller@uploadFile');
Route::post('/uploadfiledo','weixin\weixinContorller@uploadFileDo'); //上传临时素材执行
Route::post('/uploadajax','weixin\weixinContorller@uploadAjax'); //无调转显示图片
Route::get('/uploadshow','weixin\weixinContorller@uploadShow'); //无调转显示图片


//群发
Route::get('/tagmsglist','msg\MsgController@tagMsgList'); //根据标签群发
Route::get('/openmsglist','msg\MsgController@openMsgList'); //根据openid群发


Route::get('/msglist','msg\MsgController@msgList'); //消息列表


Route::post('/tagmsg','msg\MsgController@tagMsg'); //根据标签群发
Route::post('/openmsg','msg\MsgController@openMsg'); //根据openid群发
Route::post('/delmsg','msg\MsgController@delMsg'); //删除群发
Route::post('/statusmsg','msg\MsgController@statusMsg'); //查询群发消息发送状态


//模板
Route::get('/temlist','tem\TemController@temList'); //获取模板列表


Route::get('/gettem','tem\TemController@getTem'); //获取模板列表
Route::post('/deltem','tem\TemController@delTem'); //删除模板
Route::post('/sendtem','tem\TemController@sendTem'); //发送模板



//前台登陆
Route::get('/indexed','index\indexController@index'); //首页
Route::get('/index/login','index\indexController@login'); //登陆
Route::post('/index/logindo','index\indexController@loginDo'); //登陆
Route::get('/index/center','index\indexController@userCenter'); //中心
Route::get('/sesssion','index\indexController@sesssion'); //中心
Route::get('/index/part','index\indexController@part'); //商品详情 死值

Route::get('/index/record/list','weixin\weixinContorller@recordList'); //用户浏览商品记录




Route::any('/index/register','index\indexController@register'); //注册
Route::post('/index/registerdo','index\indexController@registerDo'); //注册下一步

//前台用户管理
Route::get('/index/usershow','index\indexController@userShow'); //注册下一步

Route::get('/index/quit','index\indexController@quit'); //退出


Route::get('/index/usercache','index\indexController@userCache'); //

//第三方登陆
//Route::get('/threesides','weixin\weixinContorller@threeSides'); //
Route::get('/redirecturi','weixin\weixinContorller@redirectUri'); //


//生成二维码
Route::post('/qrcode','weixin\weixinContorller@qrCode');
Route::get('/qrcode/show','weixin\weixinContorller@qrCodeShow'); //二维码用户展示


//定时器
Route::get('/setinterval','timing\timingController@setList');
Route::post('/setinterval/show','timing\timingController@setShow'); //数据展示

//订单展示
Route::get('/ordershow','indexorder\orderController@orderShow');


//微信支付
Route::get('/paytest','pay\payController@payTest'); //
Route::any('/paynotify','pay\payController@payNotify'); //

Route::get('/weixin/pay/wx_uccess/{order_id}','pay\payController@wx_uccess');   //微信支付通知回调
Route::get('/order/paysuccess/{order_id}','pay\payController@paySuccess');   //微信支付通知回调

//订单展示
Route::get('/ordershow','weixin\weixinContorller@orderShow');


//客服聊天
Route::get('/servicemsg','msg\serviceController@serviceList');
Route::post('/servicesendmsg','msg\serviceController@serviceSendMsg');
Route::post('/servicemsglist','msg\serviceController@msglist');






//Route::get('/login','weixin\weixinContorller@login'); //登陆

Route::get('/admin','weixin\weixinContorller@index'); //首页


//获取accessToken
Route::get('/accessToken','weixin\weixinContorller@accessToken');



//密钥 添加
Route::get('/insertkey','Secre\SecreController@insertKey');
//私钥 加密
Route::get('/privatekey','Secre\SecreController@privateKey');
//公钥 解密
Route::post('/publickey','Secre\SecreController@publicKey');



//测试图片切片
Route::get('/sectionlist','Test\TestController@testList');
Route::post('/sectionfrom','Test\TestController@testForm');




//考试自定义菜单
Route::get('/definedmenus/list','Kaoshi\KaoshiController@definedMenusList');

Route::post('/definedmenus/exe','Kaoshi\KaoshiController@definedMenusExe');
Route::get('/definedmenusshow','Kaoshi\KaoshiController@definedMenusShow');
Route::get('/kao/test','Kaoshi\KaoshiController@test');



//app测试
Route::post('/apptest','App\Apptest@test');


