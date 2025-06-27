<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $suppliers = Supplier::with('products')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data supplier berhasil diambil',
                'data' => $suppliers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier',
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
                'alamat' => 'required|string',
                'nomor_telepon' => 'required|string|max:20',
                'email' => 'required|email|unique:suppliers,email'
            ]);

            $supplier = Supplier::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil ditambahkan',
                'data' => $supplier
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
                'message' => 'Gagal menambahkan supplier',
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
            $supplier = Supplier::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data supplier berhasil diambil',
                'data' => $supplier
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier',
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
            $supplier = Supplier::findOrFail($id);

            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'alamat' => 'sometimes|required|string',
                'nomor_telepon' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|unique:suppliers,email,' . $id
            ]);

            $supplier->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil diperbarui',
                'data' => $supplier
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan',
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
                'message' => 'Gagal memperbarui supplier',
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
            $supplier = Supplier::findOrFail($id);
            
            // Check if supplier has products
            if ($supplier->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus supplier karena masih memiliki produk',
                ], 400);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
