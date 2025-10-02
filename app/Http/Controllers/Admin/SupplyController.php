<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Supply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Display a filtered list of supplies for the admin search widget.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));

        $supplies = Supply::query()
            ->select(['id', 'name', 'unit', 'unit_price', 'stock', 'provider_id'])
            ->with('provider:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn (Supply $supply) => [
                'id' => $supply->id,
                'name' => $supply->name,
                'unit' => $supply->unit,
                'unit_price' => $supply->unit_price,
                'stock' => $supply->stock,
                'provider' => [
                    'id' => $supply->provider?->id,
                    'name' => $supply->provider?->name,
                ],
            ]);

        return response()->json([
            'data' => $supplies,
        ]);
    }

    /**
     * Store a newly created supply in storage.
     */
    public function store(Request $request, Provider $provider): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['unit_price'] = $validated['unit_price'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;

        $provider->supplies()->create($validated);

        return redirect()
            ->route('admin.providers.show', $provider)
            ->with('status', 'Insumo agregado correctamente.');
    }

    /**
     * Remove the specified supply from storage.
     */
    public function destroy(Provider $provider, Supply $supply): RedirectResponse
    {
        if ($supply->provider_id !== $provider->id) {
            abort(404);
        }

        $supply->delete();

        return redirect()
            ->route('admin.providers.show', $provider)
            ->with('status', 'Insumo eliminado correctamente.');
    }
}
