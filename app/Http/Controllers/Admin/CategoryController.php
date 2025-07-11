<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a category listing.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? null;

        $shop = generaleSetting('rootShop');

        // Get categories with search and pagination
        $categories = $shop->categories()->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })->paginate(20);

        return view('admin.category.index', compact('categories'));
    }

    /**
     * create a new category
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * store a new category
     */
    public function store(CategoryRequest $request)
    {
        $category = CategoryRepository::storeByRequest($request);

        $shop = generaleSetting('rootShop');

        $shop->categories()->attach($category);

        return to_route('admin.category.index')->withSuccess(__('Category created successfully'));
    }

    /**
     * edit a category
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * update a category
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category = CategoryRepository::updateByRequest($request, $category);

        return to_route('admin.category.index')->withSuccess(__('Category updated successfully'));
    }

    /**
     * category status toggle
     */
    public function statusToggle(Category $category)
    {
        $category->update(['status' => ! $category->status]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->withSuccess(__('Category deleted successfully'));
    }

    public function show(Category $category)
    {
        return response()->json(['category' => $category]);
    }
}
