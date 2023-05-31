<?php

namespace App\Jobs\Navigation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Membership\Account;
use Storage;
use SimpleXMLElement;

class ParseGoogleEarthKMZJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    private $_kmlContent = null;

    private $_customer = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Account $acc, $kmlRead)
    {

        $this->_kmlContent = $kmlRead;
        $this->_customer = $acc;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->_kmlContent != null && strlen($this->_kmlContent) > 0) {
            \Log::info('KMZ Parser: Initializing new run.');
            $xmlModel = new SimpleXMLElement($this->_kmlContent);
            $sctModel = new \App\Models\Navigation\SCT($xmlModel);
            \Log::info('KMZ Parser: Starting the parser');
            $sctModel->parse();
            \Log::info('KMZ Parser: Parsing completed. Starting renderer.');
            // $result = $sctModel->render();
            $result = $sctModel->build();

            $outputFileName = 'temp/'.$this->_customer->id.'/'.\Carbon\Carbon::now()->timestamp.'.sct';
            Storage::put($outputFileName, $result);
            \Log::info('KMZ Parser: Run completed.');
            $this->_customer->notify(new \App\Notifications\Navigation\GoogleEarthKMZParsed($outputFileName));
        }
    }
}
