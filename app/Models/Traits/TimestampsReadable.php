<?php namespace App\Models\Traits;

use Carbon\Carbon;

trait TimestampsReadable
{
    private $timestampReadableFormat = 'd-m-Y (H:i)';

    public function getCreatedAtReadableAttribute()
    {
        if (!($this->created_at instanceof Carbon)) {
            return null;
        }

        // Тут выводится в часовой зоне сервера, не пользователя
        return $this->created_at->format($this->timestampReadableFormat);
    }
}