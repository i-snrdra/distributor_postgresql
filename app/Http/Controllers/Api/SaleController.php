<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $sales = Sale::with(['saleItems.product'])->orderBy('sale_date', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil diambil',
                'data' => $sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penjualan',
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
                'customer_name' => 'required|string|max:255',
                'sale_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0'
            ]);

            return DB::transaction(function () use ($validatedData) {
                $totalAmount = 0;
                $saleItems = [];

                // Validasi stok dan hitung total
                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    
                    if ($product->stok < $item['quantity']) {
                        throw new \Exception("Stok tidak mencukupi untuk produk {$product->nama}. Stok tersedia: {$product->stok}");
                    }

                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $subtotal;

                    $saleItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                        'product' => $product
                    ];
                }

                // Buat transaksi penjualan
                $sale = Sale::create([
                    'customer_name' => $validatedData['customer_name'],
                    'sale_date' => $validatedData['sale_date'],
                    'total_amount' => $totalAmount
                ]);

                // Buat detail items dan kurangi stok
                foreach ($saleItems as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Kurangi stok produk
                    $item['product']->decrement('stok', $item['quantity']);
                }

                $sale->load(['saleItems.product']);

                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil dibuat',
                    'data' => $sale
                ], 201);
            });

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat penjualan',
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
            $sale = Sale::with(['saleItems.product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil diambil',
                'data' => $sale
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penjualan',
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
            $sale = Sale::with(['saleItems.product'])->findOrFail($id);

            $validatedData = $request->validate([
                'customer_name' => 'sometimes|required|string|max:255',
                'sale_date' => 'sometimes|required|date',
                'items' => 'sometimes|required|array|min:1',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.unit_price' => 'required_with:items|numeric|min:0'
            ]);

            return DB::transaction(function () use ($sale, $validatedData) {
                // Update basic info
                if (isset($validatedData['customer_name'])) {
                    $sale->customer_name = $validatedData['customer_name'];
                }
                if (isset($validatedData['sale_date'])) {
                    $sale->sale_date = $validatedData['sale_date'];
                }

                // Update items jika ada
                if (isset($validatedData['items'])) {
                    // Kembalikan stok lama
                    foreach ($sale->saleItems as $oldItem) {
                        $oldItem->product->increment('stok', $oldItem->quantity);
                    }

                    // Hapus item lama
                    $sale->saleItems()->delete();

                    $totalAmount = 0;
                    $saleItems = [];

                    // Validasi stok baru dan hitung total
                    foreach ($validatedData['items'] as $item) {
                        $product = Product::find($item['product_id']);
                        
                        if ($product->stok < $item['quantity']) {
                            throw new \Exception("Stok tidak mencukupi untuk produk {$product->nama}. Stok tersedia: {$product->stok}");
                        }

                        $subtotal = $item['quantity'] * $item['unit_price'];
                        $totalAmount += $subtotal;

                        $saleItems[] = [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'subtotal' => $subtotal,
                            'product' => $product
                        ];
                    }

                    // Update total amount
                    $sale->total_amount = $totalAmount;

                    // Buat item baru dan kurangi stok
                    foreach ($saleItems as $item) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'subtotal' => $item['subtotal']
                        ]);

                        // Kurangi stok produk
                        $item['product']->decrement('stok', $item['quantity']);
                    }
                }

                $sale->save();
                $sale->load(['saleItems.product']);

                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil diperbarui',
                    'data' => $sale
                ], 200);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan tidak ditemukan',
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
                'message' => 'Gagal memperbarui penjualan',
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
            return DB::transaction(function () use ($id) {
                $sale = Sale::with(['saleItems.product'])->findOrFail($id);

                // Kembalikan stok sebelum menghapus
                foreach ($sale->saleItems as $item) {
                    $item->product->increment('stok', $item->quantity);
                }

                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil dihapus dan stok telah dikembalikan',
                ], 200);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penjualan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
