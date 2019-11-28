<?php

namespace App\Models;

use App\Models\Traits\TimestampsReadable;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use SpatialTrait, TimestampsReadable;

    protected $spatialFields = ['point'];

    protected $appends = ['created_at_readable'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
