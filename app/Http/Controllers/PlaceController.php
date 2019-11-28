<?php

namespace App\Http\Controllers;

use App\Helpers\GeometryHelpers;
use App\Http\Requests\Place\GetPlacesForAreaRequest;
use App\Http\Requests\Place\StorePlaceRequest;
use App\Http\Resources\PlaceCollection;
use App\Models\Place;
use App\Types\Point;
use \Grimzy\LaravelMysqlSpatial\Types\Point as DbSpatialPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaceController extends Controller
{
    private $place;

    /**
     * PlaceController constructor.
     *
     * @param Place $place
     */
    public function __construct(Place $place)
    {
        $this->place = $place;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function getForArea(GetPlacesForAreaRequest $request)
    {
        $p1 = json_decode($request->get('pointSW'));
        $p2 = json_decode($request->get('pointNE'));
        $sw = new Point($p1->lat, $p1->lng);
        $ne = new Point($p2->lat, $p2->lng);

        $polygon = GeometryHelpers::createPolygonFromSwNePoints($sw, $ne);

        return new PlaceCollection(
            $this->place
                ->with('user')
                ->within('point', $polygon)
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|array
     */
    public function store(StorePlaceRequest $request): array
    {
        $place = new $this->place;
        $place->point = new DbSpatialPoint($request->get('lat'), $request->get('lng'));
        $place->user_id = auth()->user()->id;
        $place->comment = $request->get('comment');
        $place->save();

        return compact('place');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
