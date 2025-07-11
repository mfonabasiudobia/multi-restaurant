<?php

namespace App\Repositories;

use App\Http\Requests\BannerRequest;
use App\Models\Ad;
use Illuminate\Support\Facades\Storage;

class AdsRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Ad::class;
    }

    /**
     * store new banner
     *
     * */
    public static function storeByRequest(BannerRequest $request): Ad
    {
        $thumbnail = MediaRepository::storeByRequest($request->banner, 'ads', 'image');

        return self::create([
            'title' => $request->title,
            'media_id' => $thumbnail->id,
            'redirectLink'=>$request->redirectLink,
            'status' => true,
        ]);
    }

    /**
     * Update the banner.
     */
    public static function updateByRequest($request, Ad $ad): Ad
    {
        try {
            $thumbnail = $ad->media;
            if ($request->hasFile('banner')) {
                $thumbnail = MediaRepository::updateByRequest(
                    $request->banner,
                    'ads',
                    'image',
                    $thumbnail
                );
            }
            $ad->update([
                'title' => $request->title,
                'redirectLink' => $request->redirectLink,
                'status' => $request->status??true,
                'media_id' => $thumbnail ? $thumbnail->id : null,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            // Handle the exception as needed, for example log it or rethrow it
            throw $e;
        }

        return $ad;
    }

    /**
     * delete banner
     *
     * */
    public static function destroy(Ad $ad): bool
    {
        try {
            $media = $ad->media;
            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }
            $ad->delete();
            $media->delete();
        } catch (\Exception $e) {
            // Handle the exception as needed, for example log it or return false
            return false;
        }

        return true;
    }
}
