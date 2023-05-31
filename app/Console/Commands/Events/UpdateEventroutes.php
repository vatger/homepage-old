<?php
namespace App\Console\Commands\Events;

use App\Jobs\Events\DatafeedLookupJob;
use App\Models\Events\EventRoute;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateEventroutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventroute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Work on the eventroute job.';

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
     * @return int
     */
    public function handle()
    {
        $eventroutes = EventRoute::query()->where('begins_at', '<=', Carbon::now()->utc())->get();
        foreach ($eventroutes as $eventroute)
        {
            DatafeedLookupJob::dispatch($eventroute);
        }
    }
}
