<?php namespace App\Types;

use App\Contracts\PointContract;

class Point implements PointContract
{
    /**
     * @var float
     */
    private $lat;
    /**
     * @var float
     */
    private $lng;

    /**
     * Point constructor.
     *
     * @param float $lat
     * @param float $lng
     */
    public function __construct(float $lat, float $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }


}