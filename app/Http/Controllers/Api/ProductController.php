<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::with('supplier')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'satuan' => 'required|string|max:50',
                'supplier_id' => 'required|exists:suppliers,id',
                'harga_jual' => 'required|numeric|min:0',
                'harga_beli' => 'required|numeric|min:0'
            ]);

            // Validasi bahwa harga jual lebih besar dari harga beli
            if ($validatedData['harga_jual'] <= $validatedData['harga_beli']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga jual harus lebih besar dari harga beli',
                ], 422);
            }

            $product = Product::create($validatedData);
            $product->load('supplier');

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::with('supplier')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $product
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'stok' => 'sometimes|required|integer|min:0',
                'satuan' => 'sometimes|required|string|max:50',
                'supplier_id' => 'sometimes|required|exists:suppliers,id',
                'harga_jual' => 'sometimes|required|numeric|min:0',
                'harga_beli' => 'sometimes|required|numeric|min:0'
            ]);

            // Validasi bahwa harga jual lebih besar dari harga beli
            $hargaJual = $validatedData['harga_jual'] ?? $product->harga_jual;
            $hargaBeli = $validatedData['harga_beli'] ?? $product->harga_beli;

            if ($hargaJual <= $hargaBeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga jual harus lebih besar dari harga beli',
                ], 422);
            }

            $product->update($validatedData);
            $product->load('supplier');

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by supplier
     */
    public function getBySupplier(string $supplierId): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            $products = Product::with('supplier')
                ->where('supplier_id', $supplierId)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data produk dari supplier berhasil diambil',
                'data' => [
                    'supplier' => $supplier,
                    'products' => $products
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
