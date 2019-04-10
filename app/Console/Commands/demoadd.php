<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\model\GoodsModel;

class demoadd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demoadd';

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
        $data = [
            'goods_name' => '电视机',
            'price' => '3299',
            'store' => '300',
            'add_time' => time()
        ];

        GoodsModel::insert($data);

    }
}
