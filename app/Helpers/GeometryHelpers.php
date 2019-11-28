<?php namespace App\Helpers;

use App\Contracts\PointContract;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class GeometryHelpers
{
    /**
     * @param PointContract $swPoint
     * @param PointContract $nePoint
     *
     * @return Polygon
     */
    public static function createPolygonFromSwNePoints(PointContract $swPoint, PointContract $nePoint)
    {
        return new Polygon([
            new LineString([
                new Point($swPoint->getLat(), $swPoint->getLng()),
                new Point($swPoint->getLat(), $nePoint->getLng()),
                new Point($nePoint->getLat(), $nePoint->getLng()),
                new Point($nePoint->getLat(), $swPoint->getLng()),
                new Point($swPoint->getLat(), $swPoint->getLng()),
            ])
        ]);
    }
}