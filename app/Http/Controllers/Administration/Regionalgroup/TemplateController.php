<?php


namespace App\Http\Controllers\Administration\Regionalgroup;


use App\Models\Regionalgroups\Regionalgroup;
use App\Models\Regionalgroups\RegionalgroupTemplate;
use Illuminate\Http\Request;

class TemplateController extends RegionalgroupController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if (! $request->ajax()) abort(422);

            return $next($request);
        });
    }

    public function create(Regionalgroup $regionalgroup, Request $request){
        if (! $this->hasRGPermission($regionalgroup)) abort(403);

        $validated = $request->validate(
            [
                'name' => 'required|string|max:25',
                'message' => 'required|string|max:1024',
                'order' => 'integer|min:0',
            ]
        );

        $template = new RegionalgroupTemplate();
        $template->order = $validated['order'] ? $validated['order'] : 0;
        $template->regionalgroup_id = $regionalgroup->id;
        $template->message = $validated['message'];
        $template->name = $validated['name'];
        $template->save();

        return $template;
    }

    public function edit(Regionalgroup $regionalgroup, RegionalgroupTemplate $template, Request $request){
        if (! $this->hasRGPermission($regionalgroup)) abort(403);
        if ($template->regionalgroup_id != $regionalgroup->id) abort(403);

        $validated = $request->validate(
            [
                'name' => 'required|string|max:25',
                'message' => 'required|string|max:1024',
                'order' => 'integer|min:0',
            ]
        );

        $template->order = $validated['order'] ? $validated['order'] : 0;
        $template->message = $validated['message'];
        $template->name = $validated['name'];
        $template->save();

        return $template;
    }

    public function delete(Regionalgroup $regionalgroup,RegionalgroupTemplate $template, Request $request){
        if (! $this->hasRGPermission($regionalgroup)) abort(403);
        if ($template->regionalgroup_id != $regionalgroup->id) abort(403);

        $template_id = $template->id;
        $template->delete();

        return $template;
    }
}
