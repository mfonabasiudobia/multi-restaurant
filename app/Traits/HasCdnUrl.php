<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasCdnUrl
{
    /**
     * Transform a storage URL to use CDN URL.
     *
     * @param string|null $path
     * @return string|null
     */
    public function transformUrl(?string $path = null): ?string
    {
        if (!$path) {
            return null;
        }

        // Get the S3 URL
        $s3Url = Storage::disk('s3')->url($path);
        
        // Replace the Contabo storage URL with CDN URL
        return str_replace(
            config('filesystems.disks.s3.endpoint'),
            env('CDN_URL'),
            $s3Url
        );
    }
} 