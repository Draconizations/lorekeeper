<?php

namespace App\Http\Controllers\WorldExpansion;

use App\Http\Controllers\Controller;
use App\Models\WorldExpansion\Location;
use App\Models\WorldExpansion\LocationType;
use Auth;
use Illuminate\Http\Request;
use Settings;

class LocationController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Location Controller
    |--------------------------------------------------------------------------
    |
    | This controller shows locations and their types, as well as the
    | main World Info page created in the World Expansion extension.
    |
    */

    /**
     * Shows the location types page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLocationTypes(Request $request) {
        $query = LocationType::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('worldexpansion.categories', [
            'categories'      => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
            'entry_name'      => 'location',
            'entry_names'     => 'locations',
            'category_names'  => 'types',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLocationType($id) {
        $type = LocationType::find($id);
        if (!$type) {
            abort(404);
        }

        return view('worldexpansion.category_page', [
            'category'       => $type,
            'entries'        => $type->locations,
            'category_name'  => 'type',
            'category_names' => 'types',
            'entry_name'     => 'location',
            'entry_names'    => 'locations',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLocations(Request $request) {
        $query = Location::with('type')->orderBy('sort', 'DESC');
        $data = $request->only(['type_id', 'name', 'sort']);
        if (isset($data['type_id']) && $data['type_id'] != 'none') {
            $query->where('type_id', $data['type_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'type':
                    $query->sortLocationType();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortLocationType();
        }

        if (!Auth::check() || !(Auth::check() && Auth::user()->isStaff)) {
            $query->visible();
        }

        return view('worldexpansion.entries', [
            'entries'              => $query->paginate(20)->appends($request->query()),
            'categories'           => ['none' => 'Any Type'] + LocationType::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'user_enabled'         => Settings::get('WE_user_locations'),
            'ch_enabled'           => Settings::get('WE_character_locations'),
            'entry_name'           => 'location',
            'entry_names'          => 'locations',
            'category_names'       => 'types',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLocation($id) {
        $location = Location::find($id);
        if (!$location || !$location->is_active && (!Auth::check() || !(Auth::check() && Auth::user()->isStaff))) {
            abort(404);
        }

        return view('worldexpansion.entry_page', [
            'entry'         => $location,
            'entry_name'    => 'location',
            'entry_names'   => 'locations',
            'user_enabled'  => Settings::get('WE_user_locations'),
            'loctypes'      => LocationType::get(),
            'ch_enabled'    => Settings::get('WE_character_locations'),
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLocationSubmissions($id) {
        $location = Location::find($id);
        if (!$location || !$location->is_active && (!Auth::check() || !(Auth::check() && Auth::user()->isStaff))) {
            abort(404);
        }

        return view('worldexpansion.location_submissions', [
            'location'    => $location,
            'submissions' => $location->gallerysubmissions->paginate(15),
        ]);
    }
}
