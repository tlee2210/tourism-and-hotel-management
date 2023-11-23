<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;

class categoryController extends Controller
{
    //
    public function getcategoty()
    {
        $getcategoty = Category::get();
        $data = [
            'categoty' => $getcategoty,
        ];
        return response()->json(['data' => $data], 200);
    }
    public function createCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:category',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
        ]);

        $category = Category::create([
            'name' => $data['name'],
        ]);

        return response()->json(['message' => 'Category created successfully'], 200);
    }

    public function delete($id)
    {
        $category = category::find($id);

        if (!$category) {
            return response()->json(['message' => 'The category cannot be deleted because there are associated tours'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'category deleted successfully']);
    }
}
