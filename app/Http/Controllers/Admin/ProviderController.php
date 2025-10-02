<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    /**
     * Display a listing of the providers.
     */
    public function index(): View
    {
        $providers = Provider::query()
            ->withCount('supplies')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new provider.
     */
    public function create(): View
    {
        return view('admin.providers.create');
    }

    /**
     * Store a newly created provider in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $provider = Provider::create($validated);

        return redirect()
            ->route('admin.providers.show', $provider)
            ->with('status', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified provider.
     */
    public function show(Provider $provider): View
    {
        $provider->load(['supplies' => fn ($query) => $query->orderBy('name')]);

        return view('admin.providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified provider.
     */
    public function edit(Provider $provider): View
    {
        return view('admin.providers.edit', compact('provider'));
    }

    /**
     * Update the specified provider in storage.
     */
    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $provider->update($validated);

        return redirect()
            ->route('admin.providers.show', $provider)
            ->with('status', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified provider from storage.
     */
    public function destroy(Provider $provider): RedirectResponse
    {
        $provider->delete();

        return redirect()
            ->route('admin.providers.index')
            ->with('status', 'Proveedor eliminado correctamente.');
    }
}
