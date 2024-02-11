<?php

namespace App\Console\Commands;

use App\Jobs\JobCommand;
use App\Jobs\SentToAiJob;
use Illuminate\Console\Command;

class SendImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SentToAiJob::dispatch();
    }
}
