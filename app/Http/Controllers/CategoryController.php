<?php

namespace App\Http\Controllers;

use App\Models\Category; 
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   
    public function index()
    {
        // Pega tudo o que está na tabela 'categories' e transforma em JSON
        $categories = Category::all();
        
        return response()->json($categories);
    }
}