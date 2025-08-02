<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\Repositories\SizeRepository;

class SizeController extends Controller
{
    /**
     * Display the size list.
     */
    public function index()
    {
        $shop = generaleSetting('shop');
        // $sizes = $shop->sizes()->paginate(20);
        $sizes = Size::query()->paginate(20);

        \Log::warning('View Size');

        return view('admin.size.index', compact('sizes'));
    }

    /**
     * store a new size
     */
    public function store(SizeRequest $request)
    {
        SizeRepository::storeByRequest($request);

        \Log::warning('Store Size');

        return to_route('admin.size.index')->withSuccess(__('Size created successfully'));
    }

    /**
     * update a size
     */
    public function update(SizeRequest $request, Size $size)
    {
        SizeRepository::updateByRequest($request, $size);

        \Log::warning('Update Size');

        return to_route('admin.size.index')->withSuccess(__('Size updated successfully'));
    }

    /**
     * status toggle a size
     */
    public function statusToggle(Size $size)
    {
        $size->update([
            'is_active' => ! $size->is_active,
        ]);

        \Log::warning('Toggle Size');

        return back()->withSuccess(__('Status updated successfully'));
    }
}
