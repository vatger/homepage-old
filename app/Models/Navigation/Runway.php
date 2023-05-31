<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;

class Runway extends Model {
	
	protected $table = 'navigation_runways';

	protected $fillable = [
        'ident', 'heading', 'width', 'length', 'surface_type',
    ];

    protected $appends = [
        'surfaceTypeString',
    ];

    const SURFACE_TYPE_ASPHALT = 1;
    const SURFACE_TYPE_CONCRETE = 2;
    const SURFACE_TYPE_GRASS = 3;
    const SURFACE_TYPE_WATER = 4;
    const SURFACE_TYPE_SAND = 5;
    const SURFACE_TYPE_GRE = 6;

    public function aerodrome()
    {
        return $this->belongsTo(\App\Models\Navigation\Aerodrome::class);
    }

    public function opposite()
    {
    	return $this->belongsTo(self::class, 'opposite_id', 'id');
    }

	public function getSurfaceTypeStringAttribute() {
		switch ($this->surface_type) {
            case self::SURFACE_TYPE_ASPHALT:
                return trans('pilots.aerodromes.navigation.surface.asphalt');
            case self::SURFACE_TYPE_CONCRETE:
                return trans('pilots.aerodromes.navigation.surface.concrete');
            case self::SURFACE_TYPE_GRASS:
                return trans('pilots.aerodromes.navigation.surface.grass');
            case self::SURFACE_TYPE_WATER:
                return trans('pilots.aerodromes.navigation.surface.water');
            case self::SURFACE_TYPE_SAND:
                return trans('pilots.aerodromes.navigation.surface.sand');
            case self::SURFACE_TYPE_GRE:
                return trans('pilots.aerodromes.navigation.surface.graded');
            default:
                return trans('pilots.aerodromes.navigation.surface.unkown');
        }
	}

}