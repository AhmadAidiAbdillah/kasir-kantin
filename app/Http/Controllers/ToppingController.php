<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingController extends Controller
{
    public function index()
    {
        $toppings = Topping::orderBy('name')->get();

        return view('toppings.index', compact('toppings'));
    }

    public function create()
    {
        return view('toppings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'extra_price' => 'required|integer|min:0',
        ]);

        Topping::create([
            'name'        => $request->name,
            'extra_price' => $request->extra_price,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->route('toppings.index')
            ->with('success', 'Topping berhasil ditambahkan.');
    }
}
