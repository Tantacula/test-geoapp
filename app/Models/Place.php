<?php

namespace App\Models;

use App\Models\Traits\TimestampsReadable;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use TimestampsReadable;

    protected $appends = ['created_at_readable'];

}
