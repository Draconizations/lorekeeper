<?php

namespace App\Http\Controllers\WorldExpansion;

use App\Http\Controllers\Controller;
use App\Models\WorldExpansion\Fauna;
use App\Models\WorldExpansion\FaunaCategory;
use App\Models\WorldExpansion\Flora;
use App\Models\WorldExpansion\FloraCategory;
use Auth;
use Illuminate\Http\Request;

class NatureController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Nature Controller
    |--------------------------------------------------------------------------
    |
    | This controller shows locations and their categories, as well as the
    | main World Info page created in the World Expansion extension.
    |
    */

    /**
     * Shows the faunas page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFaunaCategories(Request $request) {
        $query = FaunaCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('worldexpansion.categories', [
            'categories'       => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
            'entry_name'       => 'fauna',
            'entry_names'      => 'faunas',
            'category_names'   => 'categories',
            'category_name'    => 'category',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFaunaCategory($id) {
        $category = FaunaCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('worldexpansion.category_page', [
            'category'       => $category,
            'entries'        => $category->faunas,
            'category_name'  => 'category',
            'category_names' => 'categories',
            'entry_name'     => 'fauna',
            'entry_names'    => 'faunas',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFaunas(Request $request) {
        $query = Fauna::with('category')->orderBy('sort', 'DESC');
        $data = $request->only(['category_id', 'name', 'sort']);
        if (isset($data['category_id']) && $data['category_id'] != 'none') {
            $query->where('category_id', $data['category_id']);
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
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        if (!Auth::check() || !(Auth::check() && Auth::user()->isStaff)) {
            $query->visible();
        }

        return view('worldexpansion.entries', [
            'entries'            => $query->paginate(20)->appends($request->query()),
            'categories'         => ['none' => 'Any Category'] + FaunaCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'entry_name'         => 'fauna',
            'entry_names'        => 'faunas',
            'category_names'     => 'categories',
            'category_name'      => 'category',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFauna($id) {
        $fauna = Fauna::find($id);
        if (!$fauna || !$fauna->is_active && (!Auth::check() || !(Auth::check() && Auth::user()->isStaff))) {
            abort(404);
        }

        return view('worldexpansion.entry_page', [
            'entry'         => $fauna,
            'entry_name'    => 'fauna',
            'entry_names'   => 'faunas',
        ]);
    }

    /**
     * Shows the floras page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFloraCategories(Request $request) {
        $query = FloraCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('worldexpansion.categories', [
            'categories'       => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
            'entry_name'       => 'flora',
            'entry_names'      => 'floras',
            'category_names'   => 'categories',
            'category_name'    => 'category',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFloraCategory($id) {
        $category = FloraCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('worldexpansion.category_page', [
            'category'       => $category,
            'entries'        => $category->floras,
            'category_name'  => 'category',
            'category_names' => 'categories',
            'entry_name'     => 'flora',
            'entry_names'    => 'floras',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFloras(Request $request) {
        $query = Flora::with('category')->orderBy('sort', 'DESC');
        $data = $request->only(['category_id', 'name', 'sort']);
        if (isset($data['category_id']) && $data['category_id'] != 'none') {
            $query->where('category_id', $data['category_id']);
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
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        if (!Auth::check() || !(Auth::check() && Auth::user()->isStaff)) {
            $query->visible();
        }

        return view('worldexpansion.entries', [
            'entries'            => $query->paginate(20)->appends($request->query()),
            'categories'         => ['none' => 'Any Category'] + FloraCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'entry_name'         => 'flora',
            'entry_names'        => 'floras',
            'category_names'     => 'categories',
            'category_name'      => 'category',
        ]);
    }

    /**
     * Shows the locations page.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFlora($id) {
        $flora = Flora::find($id);
        if (!$flora || !$flora->is_active && (!Auth::check() || !(Auth::check() && Auth::user()->isStaff))) {
            abort(404);
        }

        return view('worldexpansion.entry_page', [
            'entry'         => $flora,
            'entry_name'    => 'flora',
            'entry_names'   => 'floras',
        ]);
    }
}
