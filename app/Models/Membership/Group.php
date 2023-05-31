<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Junges\ACL\Events\GroupSaving;
use Junges\ACL\Traits\ACLWildcardsTrait;
use Junges\ACL\Traits\GroupsTrait;

class Group extends Model
{
    use GroupsTrait;
    use ACLWildcardsTrait;

    protected $dates = ['deleted_at'];
    protected $table;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description',
    ];

    protected $dispatchesEvents = [
        'creating' => GroupSaving::class,
    ];

    /**
     * Group constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('acl.tables.groups'));
    }

    public function getRouteKeyName()
    {
        return config('acl.route_model_binding_keys.group_model', 'slug');
    }

    public function forumgroups() {
        return $this->belongsToMany(\App\Models\Forum\ForumGroup::class, 'forumgroup_group', 'forumgroup_id', 'group_id');
    }
}
