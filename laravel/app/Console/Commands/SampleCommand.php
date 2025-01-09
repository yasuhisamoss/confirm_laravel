<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mp:analyze {hiki}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'さんぷるさんぷる';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $your_name = $this->argument('hiki');
        print $your_name;
        print "さんぷる";
        return true;
    }
}
