<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Promotions\ActivatePromotion;
use App\Http\Requests\PromotionRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\PromotionResource;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class PromotionController extends Controller
{
    public function __construct(
        private readonly ActivatePromotion $activatePromotion
    ) {
    }

    public function index(): Response
    {
        $promotions = Promotion::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('promotions/index', [
            'promotions' => PromotionResource::collection($promotions),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('promotions/create', [
            'products' => $this->getActiveProductsList(),
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function store(PromotionRequest $request): RedirectResponse
    {
        $this->activatePromotion->execute($request->validated());

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    public function show(Promotion $promotion): Response
    {
        $promotion->load('product');

        return Inertia::render('promotions/show', [
            'promotion' => new PromotionResource($promotion),
        ]);
    }

    public function edit(Promotion $promotion): Response
    {
        return Inertia::render('promotions/edit', [
            'promotion' => new PromotionResource($promotion)->resolve(),
            'products' => $this->getActiveProductsList(),
        ]);
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($request->validated());

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    private function getActiveProductsList(): array
    {
        return ProductListResource::collection(
            Product::forList()->get()
        )->resolve();
    }
}
