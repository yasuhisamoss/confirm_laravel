<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

### php artisan app:test-batch
class TestBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'コマンドのテスト';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        print "aa";
        // app/Consts/MpConsts.phpからの読み込み
        print \MpConsts::MP_TEST;
        
        // app/Libs/MpLibs.phpからの読み込み
        $mp_libs = new \MpLibs();
        $mp_libs->get_csv();


    }
}
