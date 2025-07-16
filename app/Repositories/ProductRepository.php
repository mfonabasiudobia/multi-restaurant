<?php

namespace App\Repositories;

use App\Http\Requests\ProductRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\RecentView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Product::class;
    }

    public static function recentView(Product $product)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            RecentView::where('product_id', $product->id)->where('user_id', $user->id)->firstOrcreate([
                'product_id' => $product->id,
                'user_id' => $user->id,
            ])?->update(['updated_at' => now()]);
        }

        return $product;
    }

    /**
     * store new product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     *                                                      return \App\Models\Product
     */
    public static function storeByRequest(ProductRequest $request): Product
    {
        // Use the request object directly
        $data = $request->validated();

        // Add null checks for description fields
        $data['description'] = $request->description ?? null;
        $data['shortDescription'] = $request->shortDescription ?? null;

        // Handle main thumbnail
        $thumbnail = null;
        \Log::info('Request Data:', $data);
        if ($request->hasFile('thumbnail')) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'thumbnail');
        }

        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }


        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'short_description' => $data['shortDescription'],
            'unit_id' => $data['unit_id'],
            'price' => $data['price'],
            'discount_price' => $data['discount_price'],
            'quantity' => $data['quantity'],
            'min_order_quantity' => $data['min_order_quantity'] ?? 1,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'code' => $data['code'],
            'buy_price' => $data['buy_price'] ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'season_id' => $data['season_id'],
            'quality_id' => $data['quality_id'],
            'bag_number' => $isAdmin ? $data['bag_number'] ?? null : null,
            'location' => $isAdmin ? $data['location'] ?? null : null,
            'row' => $isAdmin ? $data['row'] ?? null : null,
        ]);

        $product->categories()->sync($data['category'] ?? []);
        $product->subcategories()->sync($data['sub_category'] ?? []);

        // Handle additional thumbnails
        if ($request->hasFile('additionThumbnail')) {
            foreach ($request->additionThumbnail as $additionThumbnail) {
                $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
                $product->medias()->attach($thumbnail->id);
            }
        }
        //videos
        if ($request->hasFile('videos')) {
            VideoRepository::storeMultipleVideos($request->file('videos'), $product->id);
        }

        // Create logistics entry if bag number exists
        if ($isAdmin && $data['bag_number']) {
            try {
                LogisticsRepository::create([
                    'article_name' => $product->name,
                    'bag_number' => $data['bag_number'],
                    'location' => $data['location'],
                    'row' => $data['row'] ?? 0,
                    'product_id' => $product->id
                ]);
            } catch (\Exception $e) {
                \Log::error('Error creating logistics entry: ' . $e->getMessage());
                throw $e; // Re-throw to handle in the controller
            }
        }

        // Handle sizes with their prices
        if ($request->has('sizeIds') && is_array($request->sizeIds)) {
            foreach ($request->sizeIds as $sizeId) {
                $sizePrice = 0;
                if (isset($request->size[$sizeId]['price'])) {
                    $sizePrice = $request->size[$sizeId]['price'];
                }

                $product->sizes()->attach($sizeId, [
                    'price' => $sizePrice
                ]);
            }
        }

        return $product;
    }

    /**
     * Update the product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \App\Models\Product
     */
    public static function updateByRequest(ProductRequest $request, Product $product): Product
    {

        \Log::info('Update Product Request Data:', $request->all());

        $thumbnail = $product->media;
        if ($request->hasFile('thumbnail') && $thumbnail) {
            $thumbnail = MediaRepository::updateByRequest(
                $request->thumbnail,
                'products',
                'image',
                $thumbnail
            );
        }
        if ($request->hasFile('thumbnail') && $thumbnail == null) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'image');
        }

        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->update_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        // Prepare update data
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => $request->shortDescription ?? null,
            'unit_id' => $request->unit_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $request->quantity,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'season_id' => $request->season_id,
            'quality_id' => $request->quality_id,
            'is_new' => false,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'is_approve' => $isAdmin ? true : $approve,
            'bag_number' => $request->bag_number ?? null,
            'location' => $request->location ?? null,
            'row' => $request->row ?? null,
        ];

        // Add shop_id to update data if root admin is updating
        if ($user->hasRole('root') && $request->filled('shop_id')) {
            $updateData['shop_id'] = $request->shop_id;
        }

        // Only filter out null values, keep empty strings and zeros
        $updateData = array_filter($updateData, function ($value) {
            return !is_null($value);
        });

        // Log the update data for debugging
        \Log::info('Product Update Data:', $updateData);

        // Update the product
        self::update($product, $updateData);

        // Handle media updates
        if ($request->is('api/*')) {
            self::updateAdditionThumbnails($request->previousThumbnail, $product);
        } else {
            foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
                $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
                $product->medias()->attach($thumbnail->id);
            }

            self::updatePreviousThumbnail($request->previousThumbnail);
        }

        // Handle videos
        if ($request->has('videos')) {
            VideoRepository::storeMultipleVideos($request->file('videos'), $product->id);
        }

        // Handle video removals
        if ($request->has('remove_videos')) {
            VideoRepository::deleteMultipleVideos($request->remove_videos, $product->id);
        }

        // Handle relationships
        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        // Handle sizes - First detach existing sizes
        // $product->sizes()->detach();

        // Get size data from request
        $sizeIds = $request->input('sizeIds', []);
        $sizeData = $request->input('size', []);

        // Log size data for debugging
        \Log::info('Processing sizes:', [
            'sizeIds' => $sizeIds,
            'sizeData' => $sizeData
        ]);


        // Attach new sizes with their prices
        // foreach ($sizeIds as $sizeId) {
        //     if (isset($sizeData[$sizeId])) {

        //         $price = $sizeData[$sizeId]['price'] ?? 0;
        //         try {
        //             $product->sizes()->attach($sizeId, ['price' => $price]);
        //             \Log::info("Attached size $sizeId with price $price");
        //         } catch (\Exception $e) {
        //             \Log::error("Error attaching size $sizeId: " . $e->getMessage());
        //         }
        //     }
        // }

        $syncData = [];

        foreach ($sizeIds as $sizeId) {
            if (isset($sizeData[$sizeId])) {
                $price = $sizeData[$sizeId]['price'] ?? 0;
                $syncData[$sizeId] = ['price' => $price];
            }
        }

        if (count($syncData) > 0) {
            $product->sizes()->sync($syncData);
        }

        // Sync VAT/taxes
        $product->vatTaxes()->sync($request->taxs ?? []);

        return $product->fresh();
    }

    /**
     * store new product from bulk import.
     */
    public static function bulkItemStore($rows, $folders = null)
    {
        $invalidRows = [];

        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        $total = 0;

        $folders = $folders !== null ? array_keys($folders) : [];

        $galleryPath = 'gallery/shop' . $shop->id;

        foreach ($rows as $row) {

            $createData = [];

            for ($i = 0; $i <= 13; $i++) {

                if ($i == 1) {
                    $createData['name'] = $row[$i];
                } elseif ($i == 2) {

                    $explodeThumbnails = explode(',', $row[$i]);

                    $thumbnails = [];
                    foreach ($explodeThumbnails as $thumbnail) {
                        $storeFile = null;
                        foreach ($folders as $folder) {
                            if (Storage::disk('public')->exists($galleryPath . '/' . $folder)) {
                                $files = File::files(Storage::disk('public')->path($galleryPath . '/' . $folder));
                                foreach ($files as $file) {
                                    if (basename($file) == $thumbnail) {
                                        $storeFile = $file;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($storeFile) {
                            $thumbnails[] = $storeFile;
                        }
                    }
                    $createData['thumbnails'] = $thumbnails;
                } elseif ($i == 3) {
                    $selectCategories = explode(',', $row[$i]);
                    $categorys = [];
                    foreach ($selectCategories as $categoryName) {

                        $category = $rootShop->categories()->where('name', $categoryName)->first();

                        if ($category) {
                            $categorys[] = $category->id;
                        }
                    }
                    $createData['categorys'] = $categorys;
                } elseif ($i == 4) {
                    $selectedSubCategories = explode(',', $row[$i]);
                    $subCategories = [];
                    foreach ($selectedSubCategories as $subCategoryName) {
                        $subCategory = $rootShop->subcategories()->where('name', $subCategoryName)->first();
                        if ($subCategory) {
                            $subCategories[] = $subCategory->id;
                        }
                    }
                    $createData['subCategories'] = $subCategories;
                } elseif ($i == 5) {
                    $selectSizes = explode(',', $row[$i]);
                    $sizes = [];
                    foreach ($selectSizes as $sizeName) {
                        $size = $rootShop->sizes()->where('name', $sizeName)->first();
                        if ($size) {
                            $sizes[] = $size->id;
                        }
                    }
                    $createData['sizes'] = $sizes;
                } elseif ($i == 6) {
                    $createData['price'] = $row[$i];
                } elseif ($i == 7) {
                    $createData['discount_price'] = $row[$i];
                } elseif ($i == 8) {
                    $createData['sku'] = $row[$i];
                } elseif ($i == 9) {
                    $createData['stock_quantity'] = $row[$i];
                } elseif ($i == 10) {
                    $createData['short_description'] = $row[$i];
                } elseif ($i == 11) {
                    $createData['description'] = $row[$i];
                }
            }

            if ($createData['name'] != null && $createData['price'] != null && count($createData['categorys']) != 0) {

                if ($createData['price'] < $createData['discount_price']) {
                    $createData['discount_price'] = $createData['price'];
                }

                self::storeBulkProduct($createData);

                $total = $total + 1;
            }
        }

        return $total;
    }

    /**
     * store new product from bulk import.
     *
     * @return Product
     */
    private static function storeBulkProduct($data)
    {
        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $thumbnail = $data['thumbnails'] ? $data['thumbnails'][0] : null;

        $media = self::storeMedia($thumbnail);

        $additionalThumbnails = $data['thumbnails'] ? array_slice($data['thumbnails'], 1) : [];
        if (isset($data['videos']) && !empty($data['videos'])) {
            foreach ($data['videos'] as $video) {
                if (Storage::disk('public')->exists($galleryPath . '/' . $video)) {
                    $videoFile = Storage::disk('public')->get($galleryPath . '/' . $video);
                    VideoRepository::storeVideo($videoFile, $product->id);
                }
            }
        }
        $medias = [];
        foreach ($additionalThumbnails as $thumbnail) {
            $hasMedia = self::storeMedia($thumbnail);
            if ($hasMedia) {
                $medias[] = $hasMedia;
            }
        }

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? 'description',
            'short_description' => $data['short_description'] ?? 'short description',
            'price' => $data['price'] ?? 0,
            'discount_price' => $data['discount_price'] ?? 0,
            'quantity' => $data['stock_quantity'] ?? 1,
            'min_order_quantity' => 1,
            'media_id' => $media,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'code' => $data['sku'] ?? random_int(100000, 999999),
        ]);

        $product->categories()->sync($data['categorys'] ?? []);
        $product->subCategories()->sync($data['subCategories'] ?? []);
        $product->sizes()->sync($data['sizes'], []);

        $product->medias()->attach($medias);

        return $product;
    }

    public static function storeMedia($thumbnail)
    {
        if ($thumbnail != null) {

            $realPath = $thumbnail->getRealPath();

            $path = 'thumbnails';

            $fileName = random_int(100000, 999999) . date('YmdHis') . '.' . pathinfo($realPath, PATHINFO_EXTENSION);

            $storagePath = Storage::disk('public')->putFileAs($path, $thumbnail, $fileName);

            $media = Media::create([
                'name' => pathinfo($storagePath, PATHINFO_FILENAME),
                'src' => $storagePath,
                'type' => 'image',
                'original_name' => basename($realPath),
                'extension' => pathinfo($storagePath, PATHINFO_EXTENSION),
            ]);

            return $media->id;
        }

        return null;
    }

    /**
     * Update the previous thumbnails.
     *
     * @param  array  $previousThumbnails  The array of previous thumbnails
     */
    private static function updatePreviousThumbnail($previousThumbnails)
    {
        foreach ($previousThumbnails ?? [] as $thumbnail) {
            if (array_key_exists('file', $thumbnail) && array_key_exists('id', $thumbnail) && $thumbnail['file'] != null) {
                $media = Media::find($thumbnail['id']);

                MediaRepository::updateByRequest(
                    $thumbnail['file'],
                    'products',
                    'image',
                    $media
                );
            }
        }
    }

    /**
     * Update the additional thumbnails.
     *
     * @param  array  $additionalThumbnails  The array of additional thumbnails
     * @param  Product  $product
     */
    private static function updateAdditionThumbnails($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->medias()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->medias()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->medias()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }
}
