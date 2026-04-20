<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService) 
    {
        $this->productService = $productService;
    }
    public function store(Request $request)
    {
        // Valida os dados enviados pelo usuário na requisição HTTP
        $validated = $request->validate([
            'name'            => 'required|min:3|max:50',            
            'description'     => 'nullable|max:200',                
            'price'           => 'required|numeric|min:0.01',       
            'expiration_date' => 'nullable|date|after_or_equal:today', 
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // repassa os dados validados para o Serviço fazer a criação no banco e salvar a imagem
            $product = $this->productService->createProduct($validated);
            return response()->json($product, 201);
            
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request)
    {
        // O controller só passa o ID do usuário logado e os parâmetros da URL (search, category_id)
        $products = $this->productService->listProducts(auth()->id(), $request->all());

        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        // Garante de forma segura que o produto existe e pertence ao usuário logado
        $product = Product::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name'            => 'sometimes|min:3|max:50',            
            'description'     => 'nullable|max:200',                
            'price'           => 'sometimes|numeric|min:0.01',       
            'expiration_date' => 'nullable|date|after_or_equal:today', 
            'category_id'     => 'sometimes|exists:categories,id',
            'image'           => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // manda o Produto encontrado e os dados novos para o Serviço atualizar
            $product = $this->productService->updateProduct($product, $validated);
            
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        // garante que o usuário logado é o dono do produto antes de permitir apagar
        $product = Product::where('user_id', auth()->id())->findOrFail($id);
        
        // repassa o produto para o Serviço apagar do Banco de Dados
        $this->productService->deleteProduct($product);
        
        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}