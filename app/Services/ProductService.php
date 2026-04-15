<?php

namespace App\Services;

use App\Models\Product;
use Exception;

class ProductService
{
    public function createProduct(array $data)
    {
        // Data de validade não pode ser no passado
        if (isset($data['expiration_date']) && $data['expiration_date'] < date('Y-m-d')) {
            throw new Exception("A data de validade não pode ser inferior à data atual.");
        }

        // Se houver arquivo, salva em storage/app/public/products
        if (isset($data['image']) && $data['image']->isValid()) {
            $path = $data['image']->store('products', 'public');
            $data['image'] = $path;
        }

        return Product::create($data);
    }
}