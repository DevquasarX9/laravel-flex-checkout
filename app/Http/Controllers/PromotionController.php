<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index(): Response
    {
        $promotions = Promotion::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($promotion) {
                return [
                    'id' => $promotion->id,
                    'product' => [
                        'id' => $promotion->product->id,
                        'sku' => $promotion->product->sku,
                        'name' => $promotion->product->name,
                        'unit_price' => (float) $promotion->product->unit_price,
                    ],
                    'quantity' => $promotion->quantity,
                    'special_price' => (float) $promotion->special_price,
                    'is_active' => $promotion->is_active,
                ];
            });

        return Inertia::render('promotions/index', [
            'promotions' => $promotions,
        ]);
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create(): Response
    {
        return Inertia::render('promotions/create', [
            'products' => $this->getActiveProductsList(),
        ]);
    }

    /**
     * Store a newly created promotion in storage.
     */
    public function store(PromotionRequest $request): RedirectResponse
    {
        // Deactivate existing active promotions for this product
        Promotion::where('product_id', $request->product_id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        Promotion::create($request->validated());

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    /**
     * Display the specified promotion.
     */
    public function show(Promotion $promotion): Response
    {
        return Inertia::render('promotions/show', [
            'promotion' => [
                'id' => $promotion->id,
                'product' => [
                    'id' => $promotion->product->id,
                    'sku' => $promotion->product->sku,
                    'name' => $promotion->product->name,
                    'unit_price' => (float) $promotion->product->unit_price,
                ],
                'quantity' => $promotion->quantity,
                'special_price' => (float) $promotion->special_price,
                'is_active' => $promotion->is_active,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promotion $promotion): Response
    {
        return Inertia::render('promotions/edit', [
            'promotion' => [
                'id' => $promotion->id,
                'product_id' => $promotion->product_id,
                'quantity' => $promotion->quantity,
                'special_price' => (float) $promotion->special_price,
                'is_active' => $promotion->is_active,
            ],
            'products' => $this->getActiveProductsList(),
        ]);
    }

    /**
     * Update the specified promotion in storage.
     */
    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($request->validated());

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    /**
     * Remove the specified promotion from storage.
     */
    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    /**
     * Get active products list for dropdowns.
     */
    private function getActiveProductsList(): Collection
    {
        return Product::getActiveForList();
    }
}
