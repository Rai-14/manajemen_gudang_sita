<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Batasi akses hanya untuk Admin dan Manager
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('categories', 'public');
        }

        Category::create(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    // Kita akan biarkan method show, edit, update, destroy tetap kosong/minimal
    // dan melengkapinya di langkah berikutnya.

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used for simple categories, redirect to index
        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate($this->rules($category->id));

        $imagePath = $category->image_path; 
        if ($request->hasFile('image_path')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_path')->store('categories', 'public');
        }

        $category->update(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Validation: Cannot delete if products exist
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Gagal menghapus! Kategori ini masih memiliki ' . $category->products()->count() . ' produk terkait.');
        }

        // Delete image if exists
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    /**
     * Validation rules for store and update.
     */
    protected function rules($ignoreId = null)
    {
        return [
            // Nama kategori harus unik
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('categories')->ignore($ignoreId)
            ],
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];
    }
}