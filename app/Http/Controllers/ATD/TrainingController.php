<?php

namespace App\Http\Controllers\ATD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UTS\ATD\Training;
use App\Models\UTS\ATD\TrainingSession;
use App\Models\Regionalgroups\Regionalgroup;

class TrainingController extends Controller
{

	private $customParsers = [
        'color' => [
            'pattern' => '/\[color\=(.*?)\](.*?)\[\/color]/s',
            'replace' => '<span style="color: $1">$2</span>',
            'content' => '$2',
        ],
        'center' => [
            'pattern' => '/\[center\](.*?)\[\/center]/s',
            'replace' => '<span class="text-center">$1</span>',
            'content' => '$1',
        ],
        'size' => [
            'pattern' => '/\[size\=(.*?)\](.*?)\[\/size]/s',
            'replace' => '<span style="font-size: 1.1$1rem">$2</span>',
            'content' => '$2',
        ],
        'table' => [
            'pattern' => '/\[table\]\n?(.*?)\[\/table\]\n?/s',
            'replace' => '<table class="table table-responsive-sm table-hover table-borderless">$1</table>',
            'content' => '$1',
        ],
        'table-row' => [
            'pattern' => '/\[tr\]\n?(.*?)\[\/tr\]\n?/s',
            'replace' => '<tr>$1</tr>',
            'content' => '$1',
        ],
        'table-data' => [
            'pattern' => '/\[td\]\n?(.*?)\[\/td\]\n?/s',
            'replace' => '<td>$1</td>',
            'content' => '$1',
        ],
        'table-head' => [
            'pattern' => '/\[th\]\n?(.*?)\[\/th]\n?/s',
            'replace' => '<th>$1</th>',
            'content' => '$1',
        ],
        'link' => [
            'pattern' => '/\[url\](.*?)\[\/url\]/s',
            'replace' => '<a href="$1">$1</a>',
            'content' => '$1'
        ],
        'namedlink' => [
            'pattern' => '/\[url\=\'?(.*?)\'?\](.*?)\[\/url\]/s',
            'replace' => '<a href="$1">$2</a>',
            'content' => '$2'
        ],
    ];
    
	function __construct()
	{
		parent::__construct();
	}

	public function index(Request $request)
	{
		if($request->ajax()) {
			$trainings = Training::forTrainee($this->account->id)->with('regionalgroup.fir', 'sessions.station', 'sessions.mentor', 'sessions.secondMentor')->get();

			return $trainings;
		}
	}

	public function create(Request $request)
	{
		if($request->ajax()) {

			$validated = $request->validate(
				[
					'rg' => 'required|exists:regionalgroups_regionalgroups,id',
				]
			);

			// Check if the user is at least a guest member of that regionalgroup
			$rg = Regionalgroup::find($validated['rg']);
			if($this->account->isMemberOfRegionalgroup($rg) || $this->account->isGuestOfRegionalgroup($rg)) {

				// Disable training requests until further notice from VATEUD
				// regarding European trainingssystems.
				// 
				// // Check if there is a training already for that regionalgroup.
				// $openTraining = Training::where('trainee_id', $this->account->id)->where('regionalgroup_id', $validated['rg'])->exists();
				// if($openTraining) {
				// 	// Send a broadcast notification the the user
				// 	$this->account->notify(new \App\Notifications\ATD\TrainingAlreadyRequestedNotification);
				// } else {
				// 	$training = new Training;
				// 	$training->trainee_id = $this->account->id;
				// 	$training->regionalgroup_id = $validated['rg'];
				// 	$training->save();
				// }
				// return response()->json(['redirect' => true]);
				// 
				$this->account->notify(new \App\Notifications\ATD\SeeForumNotification);
				return response()->json(['redirect' => false]);
			} else {
				$this->account->notify(new \App\Notifications\ATD\NoMemberOfRegionalgroupNotification($rg->name));
				return response()->json(['redirect' => false]);
			}
		}
	}

	public function createSession(Request $request, Training $training)
	{
		if($request->ajax()) {
			$validated = $request->validate(
				[
					'newSessionRequest.type' => 'required|in:1,2,3',
					'newSessionRequest.station' => 'required|exists:navigation_stations,id',
					'newSessionRequest.start' => 'required|date_format:"d.m.Y H:i"',
					'newSessionRequest.end' => 'required|date_format:"d.m.Y H:i"',
				]
			);

			$openRequests = $training->sessions()->where('accepted', false)->exists();
			if($openRequests) {
				// Break here and send a broadcast notification the the user
				$this->account->notify(new \App\Notifications\ATD\SessionAlreadyRequestedNotification);
				die;
			}

			$newSession = new TrainingSession;
			$newSession->training_id = $training->id;
			$newSession->type = $validated['newSessionRequest']['type'];
			$newSession->station_id = $validated['newSessionRequest']['station'];
			$newSession->started_at = $validated['newSessionRequest']['start'];
			$newSession->ended_at = $validated['newSessionRequest']['end'];
			$newSession->save();

			$newSession->loadMissing('station');

			return $newSession;
		}
	}

	public function removeSession(Request $request, Training $training, TrainingSession $trainingsession)
	{
		if($request->ajax()) {
			$result = $trainingsession->delete();
			return response()->json(['result' => $result]);
		}
	}

	public function getAvailableRegionalgroups(Request $request)
	{
		if($request->ajax()) {
			return Regionalgroup::orderBy('name', 'ASC')->get();
		}
	}

	/**
     * Get the regionalgroup training status from the forum
     * @return [type] [description]
     */
    public function status()
    {
        if( $this->account->setting->language == 'de') {
            $post = \App\Libraries\XenBridge::getPost('833800')->post;
        }
        else{
            $post = \App\Libraries\XenBridge::getPost('833802')->post;
        }

        $status = $post->message;

        $bbCode = new \Genert\BBCode\BBCode();
        $bbCode->addLinebreakParser();
        foreach ($this->customParsers as $name => $parser) {
            $bbCode->addParser(
                $name,
                $parser['pattern'],
                $parser['replace'],
                $parser['content'],
            );
        }
        // Parse the message through the bbcode
        $status = $bbCode->convertToHtml($status, \Genert\BBCode\BBCode::CASE_SENSITIVE);
        $status = $bbCode->stripBBCodeTags($status);

        return response()->json(
            $status,
            200
        );
    }

}
