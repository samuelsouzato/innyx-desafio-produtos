<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;

    // injeta o Service automaticamente aqui (Dependency Injection)
    public function __construct(ProductService $productService) 
    {
        $this->productService = $productService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'description' => 'required',
            'price' => 'required|numeric',
            'expiration_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $product = $this->productService->createProduct($validated);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request)
{
    // consulta base
    $query = \App\Models\Product::query();

    // termo de busca (?search=teclado)
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
    }

    // retorna paginado (10 por página)
    return response()->json($query->paginate(10));
}

    public function update(Request $request, $id)
{
    $product = \App\Models\Product::findOrFail($id);

    $validated = $request->validate([
        'name'            => 'sometimes|min:3',
        'description'     => 'sometimes',
        'price'           => 'sometimes|numeric',
        'expiration_date' => 'sometimes|date',
        'category_id'     => 'sometimes|exists:categories,id',
        'image'           => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    if ($request->hasFile('image')) {
        // deleta a imagem física antiga se ela existir
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        // salva a nova imagem
        $path = $request->file('image')->store('products', 'public');
        $validated['image'] = $path;
    }

    $product->update($validated);

    return response()->json($product);
}
public function destroy($id)
{
    $product = \App\Models\Product::findOrFail($id);
    
    // Apaga o arquivo antes de deletar o registro
    if ($product->image) {
        Storage::disk('public')->delete($product->image);
    }
    
    $product->delete();

    return response()->json(['message' => 'Produto removido com sucesso']);
}
}