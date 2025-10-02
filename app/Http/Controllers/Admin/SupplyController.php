<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
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
