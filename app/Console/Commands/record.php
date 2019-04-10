<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\model\RecordMoel;

class record extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $list_key = 'list_record';
        $data = Redis::lrange($list_key,0,-1);
        $arr = [];
        foreach($data as $v){
            $da = Redis::hgetall($v);
            array_push($arr,$da);
        }

        //存数据库
        $res = RecordMoel::insert($arr);
        if($res){
            Redis::del($list_key);
        }

    }
}
