<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Empresa::class);

        $query = Empresa::query()->withCount(['sucursales', 'users', 'items']);

        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('identificacion_fiscal', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $empresas = $query->latest()->paginate(10)->withQueryString();

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        $this->authorize('create', Empresa::class);
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Empresa::class);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion_fiscal' => 'required|string|max:50|unique:empresas,identificacion_fiscal',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
            'estado' => 'required|in:activo,inactivo',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('empresas', 'public');
        }

        Empresa::create($validated);

        return redirect()->route('empresas.index')->with('success', 'Empresa registrada correctamente.');
    }

    public function show(Empresa $empresa)
    {
        $this->authorize('view', $empresa);
        $empresa->load(['sucursales.areas.encargado', 'categorias', 'proveedores']);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $this->authorize('update', $empresa);
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $this->authorize('update', $empresa);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificacion_fiscal' => 'required|string|max:50|unique:empresas,identificacion_fiscal,' . $empresa->id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
            'estado' => 'required|in:activo,inactivo',
        ]);

        if ($request->hasFile('logo')) {
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $validated['logo'] = $request->file('logo')->store('empresas', 'public');
        }

        $empresa->update($validated);

        return redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa)
    {
        $this->authorize('delete', $empresa);

        // Validar si tiene stock activo en sus áreas
        $hasStock = $empresa->items()->whereHas('inventarios', function ($q) {
            $q->where('cantidad', '>', 0);
        })->exists();

        if ($hasStock) {
            return back()->with('error', 'No se puede eliminar la empresa porque posee ítems con stock activo en inventario.');
        }

        $empresa->delete();

        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada lógicamente.');
    }
}
