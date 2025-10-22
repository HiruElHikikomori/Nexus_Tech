<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product; // Importa el modelo Product
use App\Models\ProductType; // Importa el model ProductType
use App\Models\CartItem; // ¡Importa el modelo CartItem!
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->input('query');

        $products = Product::query();

        if ($query) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            });
        }

        // --- CAMBIO AQUÍ: Usar paginate() en lugar de get() ---
        // Puedes especificar cuántos elementos por página quieres, por ejemplo, 10.
        // El método ->appends(request()->query()) es crucial para mantener los parámetros de la búsqueda en la URL de paginación.
        $products = $products->paginate(10)->appends(request()->query());

        $productTypes = ProductType::all();
        
        return view('admin.productsTable', compact('products', 'productTypes'));
    }

    public function RandomProductOrder(){
        $randProducts = Product::inRandomOrder()->take(4)->get();
        $productTypes = ProductType::all();
        return view('index', compact('randProducts', 'productTypes'));
    }

    public function ProductUser(Request $request)
    {
        // 1. Obtener el término de búsqueda de la URL (para la barra de búsqueda)
        $query = $request->input('query');

        // 2. Obtener los parámetros de filtro y ordenamiento
        $productTypeId = $request->input('product_type_id');
        $sortByName = $request->input('sort_by_name');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Iniciar la consulta de productos
        $products = Product::query();

        // Aplicar la búsqueda por nombre/descripción (si hay)
        if ($query) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            });
        }

        // Aplicar el filtro por tipo de producto (si hay)
        if ($productTypeId) {
            $products->where('product_type_id', $productTypeId);
        }

        // Aplicar el filtro por precio mínimo (si hay un valor, incluso 0)
        // Convertimos a float para asegurar la comparación correcta
        if ($minPrice !== null && $minPrice !== '') {
            $products->where('price', '>=', (float)$minPrice);
        }

        // Aplicar el filtro por precio máximo (si hay un valor)
        if ($maxPrice !== null && $maxPrice !== '') {
            $products->where('price', '<=', (float)$maxPrice);
        }

        // Aplicar el ordenamiento por nombre (si hay)
        if ($sortByName === 'asc') {
            $products->orderBy('name', 'asc');
        } elseif ($sortByName === 'desc') {
            $products->orderBy('name', 'desc');
        }

        // Obtener los productos (filtrados o todos)
        $products = $products->get();

        // Obtener todos los tipos de producto para el dropdown del filtro
        $productTypes = ProductType::all();

        // Obtener el precio máximo actual de todos los productos para el slider de rango
        // Esto es importante para establecer el 'max' correcto en el input range.
        $maxProductPrice = Product::max('price');
        // Si no hay productos, o max devuelve null, establece un valor por defecto sensato.
        if ($maxProductPrice === null) {
            $maxProductPrice = 1000; // Un valor por defecto, ajústalo según tus productos
        } else {
             // Redondear hacia arriba a la próxima decena, centena, etc., para una barra más "suave"
             // Por ejemplo, si el máximo es 95, el slider irá a 100. Si es 950, a 1000.
            $maxProductPrice = ceil($maxProductPrice / 100) * 100;
            // Asegurarse de que no sea 0 si hay productos pero el precio máximo es muy bajo.
            if ($maxProductPrice == 0 && Product::count() > 0) {
                 $maxProductPrice = 100;
            }
        }


        // Pasa los productos, tipos de producto y el precio máximo a la vista
        return view('products', compact('products', 'productTypes', 'maxProductPrice'));
    }

    // El método search ya no es estrictamente necesario, ya que ProductUser maneja todo.
    // Si la ruta `products.search` apunta a este método, puedes redirigir o simplemente llamarlo.
    public function search(Request $request)
    {
        return $this->ProductUser($request); // Reutiliza la lógica principal
    }

    public function SearchAdmin(Request $request){
        return $this->index($request);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'product_type_id' => 'required|exists:product_types,product_type_id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'img_name' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2040',
        ]);

        $filename = 'default.png';

        if($request->hasFile('img_name')){
            $file = $request->file('img_name');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/products'), $filename);
        }else{

            $filename = 'default.png';
        }

        Product::create([
            'name' => $request->name,
            'product_type_id' => $request->product_type_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'img_name' => $filename,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado con exito');
    }
    public function update(Request $request, Product $product)
    {
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'product_type_id' => 'required|exists:product_types,product_type_id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'img_name' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2040',
        ]);

        if ($request->hasFile('img_name')) {
            // Si se sube una nueva imagen, eliminar la antigua (si no es la por defecto)
            if ($product->img_name && $product->img_name !== 'default.png') {
                $oldImagePath = public_path('img/products/' . $product->img_name);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                
            }
            // Almacena la nueva imagen
            $file = $request->file('img_name');
            $filename = time() . '_' . $file->getClientOriginalExtension();
            $file->move(public_path('img/products'), $filename);
            $product->img_name = $filename;
            $validatedData['img_name'] = $filename; // Asignar el nuevo nombre de archivo
        } else {
            // Si NO se sube una nueva imagen, usar la imagen existente del producto.
            // Si $product->img_name es null, usar 'default.png'.
            $validatedData['img_name'] = $product->img_name ?? 'default.png';
        }

        // Ya tienes el objeto $product gracias al Route Model Binding, no necesitas Product::find($product)
        $product->update($validatedData); // Esto actualiza todos los campos de golpe

        //dd('Producto después de actualizar (recargado de DB):', $product->fresh()->toArray());

        // Redirigir a una ruta apropiada, por ejemplo, la lista de productos o el detalle del producto
        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado con éxito');
        // O si quieres ir al detalle del producto:
        // return redirect()->route('admin.products.show', $product)->with('success', 'Producto actualizado con éxito');
    }

    public function destroy(Product $product)
    {
        //Verifica si el producto está en algún carrito
        $isInCart = CartItem::where('products_id', $product->products_id)->exists();

        if($isInCart){
            CartItem::where('products_id', $product->products_id)->delete();
        }

        // Implementar la lógica para eliminar la imagen del almacenamiento
        if ($product->img_name && $product->img_name != 'default.png') {
            $imagePath = public_path('img/products/' . $product->img_name);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado con éxito');
    }
}
