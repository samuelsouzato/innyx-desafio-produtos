<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function createProduct(array $data)
    {
        if (isset($data['expiration_date']) && $data['expiration_date'] < date('Y-m-d')) {
            throw new Exception("A data de validade não pode ser inferior à data atual.");
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        $data['user_id'] = auth()->id(); 
        return Product::create($data);
    }

    // listagem e filtros
    public function listProducts(int $userId, array $filters)
    {
        $query = Product::where('user_id', $userId)->with('category'); 

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->paginate(5);
    }

    public function updateProduct(Product $product, array $data)
    {
        if (isset($data['expiration_date']) && $data['expiration_date'] < date('Y-m-d')) {
            throw new Exception("A data de validade não pode ser inferior à data atual.");
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            if ($product->getRawOriginal('image')) {
                Storage::disk('public')->delete($product->getRawOriginal('image'));
            }
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);
        return $product;
    }

    // deleta arquivos e do banco 
    public function deleteProduct(Product $product)
    {
        if ($product->getRawOriginal('image')) {
            Storage::disk('public')->delete($product->getRawOriginal('image'));
        }
        
        return $product->delete();
    }
}