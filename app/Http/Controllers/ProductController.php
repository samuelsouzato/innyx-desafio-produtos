<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService) 
    {
        $this->productService = $productService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|min:3|max:50',            
            'description'     => 'nullable|max:200',                
            'price'           => 'required|numeric|min:0.01',       
            'expiration_date' => 'nullable|date|after_or_equal:today', 
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'required|image|mimes:jpeg,png,jpg|max:2048'
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
        // filtra apenas os produtos do utilizador logado!
        $query = \App\Models\Product::where('user_id', auth()->id())->with('category'); 

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        

        return response()->json($query->paginate(5));
    }

    public function update(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name'            => 'sometimes|min:3|max:50',            
            'description'     => 'nullable|max:200',                
            'price'           => 'sometimes|numeric|min:0.01',       
            'expiration_date' => 'nullable|date|after_or_equal:today', 
            'category_id'     => 'sometimes|exists:categories,id',
            'image'           => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $product = $this->productService->updateProduct($product, $validated);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        // 💡 Novamente: getRawOriginal para deletar o arquivo físico corretamente
        if ($product->getRawOriginal('image')) {
            Storage::disk('public')->delete($product->getRawOriginal('image'));
        }
        
        $product->delete();
        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}