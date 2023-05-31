<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 *
 */
class ForumController extends Controller
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
        'attach' => [
        	'pattern' => '/\[ATTACH type\=\"(.*?)\"\](.*?)\[\/ATTACH\]/s',
        	'replace' => '',
        	'content' => '',
        ],
	];

	function __construct()
	{
		parent::__construct();
	}

	public static function sortByDateStamp($a, $b)
	{
		if($a->post_date == $b->post_date) return 0;
		return $a->post_date < $b->post_date ? -1 : 1;
	}

	public function getNews(Request $request)
	{

	    $threads = \App\Libraries\XenBridge::getNewsThreads();
        if($threads == false) abort(503, 'forum unavailable');

		$newsThreads = $threads->sticky;

		$ordered = usort($newsThreads, array("self", "sortByDateStamp"));

		if($ordered) {
			$newsThreads = array_reverse($newsThreads);
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
			foreach ($newsThreads as $nt) {
				// Get all the firsts posts from the threads
				$nt->post = \App\Libraries\XenBridge::getPost($nt->first_post_id)->post;
				$nt->post->parsedMessage = $bbCode->convertToHtml($nt->post->message, \Genert\BBCode\BBCode::CASE_SENSITIVE);
				$nt->post->parsedMessage = $bbCode->stripBBCodeTags($nt->post->parsedMessage);
			}
			return $newsThreads;
		}
	}
}
