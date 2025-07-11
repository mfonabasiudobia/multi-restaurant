<?php

namespace App\Repositories;

use App\Http\Requests\SubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Support\Str;

class SubCategoryRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return SubCategory::class;
    }

    /**
     * store a new category
     */
    public static function storeByRequest(SubCategoryRequest $request): SubCategory
    {
        $shop = generaleSetting('rootShop');
    
        \Log::info('Creating SubCategory', $request->all());
    
        $subCategory = self::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'is_active' => true,
        ]);
    
        // Ensure categories exist and attach them
        if ($request->has('categories') && is_array($request->categories)) {
            \Log::info('Attaching Categories:', ['categories' => $request->categories]);
            $subCategory->categories()->attach($request->categories);
        } else {
            \Log::warning('No categories provided for attachment', ['categories' => $request->categories ?? null]);
        }
    
        return $subCategory;
    }

    /**
     * update a category
     */
    public static function updateByRequest(SubCategoryRequest $request, SubCategory $subCategory): SubCategory
    {
        // $thumbnail = $subCategory->media;
        // if ($request->hasFile('thumbnail')) {
        //     $thumbnail = MediaRepository::updateByRequest(
        //         $request->file('thumbnail'),
        //         'categories',
        //         'image',
        //         $thumbnail
        //     );
        // }

        $subCategory->update([
            'name' => $request->name,
            'media_id' => $thumbnail->id ?? $subCategory->media_id,
            'slug' => Str::slug($request->name, '-'),
        ]);

        $subCategory->categories()->sync($request->categories);

        return $subCategory;
    }
}