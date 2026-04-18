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

    public function updateProduct(Product $product, array $data)
    {
        if (isset($data['expiration_date']) && $data['expiration_date'] < date('Y-m-d')) {
            throw new Exception("A data de validade não pode ser inferior à data atual.");
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            // 💡 O segredo: usar getRawOriginal para pegar o caminho real do banco, sem a URL do Accessor
            if ($product->getRawOriginal('image')) {
                Storage::disk('public')->delete($product->getRawOriginal('image'));
            }
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);
        return $product;
    }
}