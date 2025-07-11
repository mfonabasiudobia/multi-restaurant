<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Log the request data for debugging
        \Log::info('ProductRequest data:', [
            'all' => $this->all(),
            'sizeIds' => $this->sizeIds,
            'size' => $this->size,
        ]);

        // Log the validation rules for debugging
        $rules = [
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'shortDescription' => 'nullable|string|max:191',
            'category' => 'required|exists:categories,id',
            'sub_category' => 'nullable|array|exists:sub_categories,id',
            'code' => 'required|numeric|digits_between:5,25',
            'color' => 'nullable|array',
            'size' => 'nullable|array',
            'size.*.id' => 'nullable|exists:sizes,id',
            'size.*.name' => 'nullable|string',
            'size.*.price' => 'nullable|numeric|min:0',
            'sizeIds' => 'nullable|array',
            'sizeIds.*' => 'exists:sizes,id',
            'size_remove' => 'nullable|array',
            'size_remove.*' => 'exists:sizes,id',
            'unit_id' => 'required|exists:units,id',
            'buy_price' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_order_quantity' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4048',
            'additionThumbnail' => 'nullable|array',
            'additionThumbnail.*' => 'image|mimes:png,jpg,jpeg,webp|max:4048',
            'previousThumbnail' => 'nullable|array',
            'previousThumbnail.*.id' => 'nullable|exists:media,id',
            'previousThumbnail.*.file' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'videos.*' => 'nullable|file|mimes:mp4,avi,mov|max:111200',
            'remove_videos' => 'nullable|array',
            'remove_videos.*' => 'exists:videos,id',
            'season_id' => 'nullable|exists:seasons,id',
            'quality_id' => 'nullable|exists:qualities,id',
            'bag_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'row' => 'nullable|integer',
            'shop_id' => 'nullable|exists:shops,id',
        ];

        \Log::info('Validation rules:', $rules);

        return $rules;
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            $lan = $request->header('accept-language') ?? 'en';
            app()->setLocale($lan);
        }

        return [
            'name.required' => __('The name field is required.'),
            'name.max' => __('The name may not be greater than 191 characters.'),
            'description.required' => __('The description field is required.'),
            'shortDescription.required' => __('The short description field is required.'),
            'shortDescription.max' => __('The short description may not be greater than 191 characters.'),
            'category.required' => __('The category field is required.'),
            'category.exists' => __('The selected category is invalid.'),
            'code.required' => __('The code field is required.'),
            'code.unique' => __('The code has already been taken.'),
            'code.numeric' => __('The code must be a number.'),
            'code.digits_between' => __('The code must be 5-7 digits.'),
            'unit_id.required' => __('The unit field is required.'),
            'unit_id.exists' => __('The selected unit is invalid.'),
            'price.required' => __('The price field is required.'),
            'price.numeric' => __('The price must be a number.'),
            'price.min' => __('The price must be at least 0.'),
            'discount_price.numeric' => __('The discount price must be a number.'),
            'discount_price.min' => __('The discount price must be at least 0.'),
            'quantity.required' => __('The quantity field is required.'),
            'quantity.integer' => __('The quantity must be an integer.'),
            'quantity.min' => __('The quantity must be at least 0.'),
            'min_order_quantity.required' => __('The min order quantity field is required.'),
            'min_order_quantity.integer' => __('The min order quantity must be an integer.'),
            'min_order_quantity.min' => __('The min order quantity must be at least 0.'),
            'thumbnail.image' => __('The thumbnail must be an image.'),
            'thumbnail.mimes' => __('The thumbnail must be a file of type: png, jpg, jpeg, webp.'),
            'thumbnail.max' => __('The thumbnail may not be greater than 2048 kilobytes.'),
            'additionThumbnail.required' => __('The addition thumbnail field is required.'),
            'additionThumbnail.image' => __('The addition thumbnail must be an image.'),
            'additionThumbnail.mimes' => __('The addition thumbnail must be a file of type: png, jpg, jpeg, webp.'),
            'additionThumbnail.max' => __('The addition thumbnail may not be greater than 2048 kilobytes.'),
            'videos.*.mimes' => __('The videos must be a file of type: mp4, avi, mov.'),
        ];
    }
}
