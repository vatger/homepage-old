<?php

namespace App\Models\Navigation;

use App\Libraries\Gitlab;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{

	protected $table = 'navigation_charts';

    protected $appends = ['is_gitlab', 'gitlab_link'];

	public function aerodromes()
	{
		return $this->belongsToMany(Aerodrome::class, 'navigation_aerodrome_chart', 'chart_id', 'aerodrome_id');
	}

	public function scopeAirac($query, $airac = '')
	{
		if($airac === '') {
			$airac = \Carbon\Carbon::now()->utc()->format('ym');
		}
		return $query->where('airac', $airac);
	}

	public function scopePublished($query, $published = true)
	{
		return $query->where('published', $published);
	}

    public function getIsGitlabAttribute() : bool
    {
        return str_starts_with($this->href, 'gitlab:');
    }

    public function getGitlabLinkAttribute()
    {
        $gitlab = new Gitlab();
        return $gitlab->generateChartLink($this);
    }



}
