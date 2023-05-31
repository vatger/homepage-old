<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

class ForumGroup extends Model
{
    
	protected $table = 'forumgroups';

	public function groups() {
		return $this->belongsToMany(config('acl.models.group'), 'forumgroup_group', 'group_id', 'forumgroup_id');
	}

}