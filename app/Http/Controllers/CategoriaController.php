<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Categoria::class);

        $query = Categoria::query()->withCount('items');

        $empresaId = $this->resolveEmpresaId();
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->get('buscar') . '%');
        }

        $categorias = $query->latest()->paginate(10)->withQueryString();

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        $this->authorize('create', Categoria::class);
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Categoria::class);

        $empresaId = auth()->user()->empresa_id;

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $validated['empresa_id'] = $empresaId;

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada con éxito.');
    }

    public function edit(Categoria $categoria)
    {
        $this->authorize('update', $categoria);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('update', $categoria);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada con éxito.');
    }

    public function destroy(Categoria $categoria)
    {
        $this->authorize('delete', $categoria);

        if ($categoria->items()->exists()) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene ítems asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada lógicamente.');
    }
}
