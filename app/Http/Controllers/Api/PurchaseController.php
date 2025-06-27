<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $purchases = Purchase::with(['supplier', 'purchaseItems.product'])
                ->orderBy('purchase_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data pembelian berhasil diambil',
                'data' => $purchases
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembelian',
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
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0'
            ]);

            return DB::transaction(function () use ($validatedData) {
                $totalAmount = 0;
                $purchaseItems = [];

                // Hitung total dan validasi produk supplier
                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    
                    // Validasi bahwa produk berasal dari supplier yang sama
                    if ($product->supplier_id != $validatedData['supplier_id']) {
                        throw new \Exception("Produk {$product->nama} tidak berasal dari supplier yang dipilih");
                    }

                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $subtotal;

                    $purchaseItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                        'product' => $product
                    ];
                }

                // Buat transaksi pembelian
                $purchase = Purchase::create([
                    'supplier_id' => $validatedData['supplier_id'],
                    'purchase_date' => $validatedData['purchase_date'],
                    'total_amount' => $totalAmount
                ]);

                // Buat detail items dan tambah stok
                foreach ($purchaseItems as $item) {
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Tambah stok produk
                    $item['product']->increment('stok', $item['quantity']);
                }

                $purchase->load(['supplier', 'purchaseItems.product']);

                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil dibuat',
                    'data' => $purchase
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
                'message' => 'Gagal membuat pembelian',
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
            $purchase = Purchase::with(['supplier', 'purchaseItems.product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data pembelian berhasil diambil',
                'data' => $purchase
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pembelian tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembelian',
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
            $purchase = Purchase::with(['supplier', 'purchaseItems.product'])->findOrFail($id);

            $validatedData = $request->validate([
                'supplier_id' => 'sometimes|required|exists:suppliers,id',
                'purchase_date' => 'sometimes|required|date',
                'items' => 'sometimes|required|array|min:1',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.unit_price' => 'required_with:items|numeric|min:0'
            ]);

            return DB::transaction(function () use ($purchase, $validatedData) {
                // Update basic info
                if (isset($validatedData['supplier_id'])) {
                    $purchase->supplier_id = $validatedData['supplier_id'];
                }
                if (isset($validatedData['purchase_date'])) {
                    $purchase->purchase_date = $validatedData['purchase_date'];
                }

                // Update items jika ada
                if (isset($validatedData['items'])) {
                    // Kurangi stok lama (balik ke kondisi sebelum pembelian)
                    foreach ($purchase->purchaseItems as $oldItem) {
                        $oldItem->product->decrement('stok', $oldItem->quantity);
                    }

                    // Hapus item lama
                    $purchase->purchaseItems()->delete();

                    $totalAmount = 0;
                    $purchaseItems = [];

                    $supplierId = $validatedData['supplier_id'] ?? $purchase->supplier_id;

                    // Validasi dan hitung total baru
                    foreach ($validatedData['items'] as $item) {
                        $product = Product::find($item['product_id']);
                        
                        // Validasi bahwa produk berasal dari supplier yang sama
                        if ($product->supplier_id != $supplierId) {
                            throw new \Exception("Produk {$product->nama} tidak berasal dari supplier yang dipilih");
                        }

                        $subtotal = $item['quantity'] * $item['unit_price'];
                        $totalAmount += $subtotal;

                        $purchaseItems[] = [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'subtotal' => $subtotal,
                            'product' => $product
                        ];
                    }

                    // Update total amount
                    $purchase->total_amount = $totalAmount;

                    // Buat item baru dan tambah stok
                    foreach ($purchaseItems as $item) {
                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'subtotal' => $item['subtotal']
                        ]);

                        // Tambah stok produk
                        $item['product']->increment('stok', $item['quantity']);
                    }
                }

                $purchase->save();
                $purchase->load(['supplier', 'purchaseItems.product']);

                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil diperbarui',
                    'data' => $purchase
                ], 200);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pembelian tidak ditemukan',
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
                'message' => 'Gagal memperbarui pembelian',
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
                $purchase = Purchase::with(['purchaseItems.product'])->findOrFail($id);

                // Kurangi stok sebelum menghapus (balik ke kondisi sebelum pembelian)
                foreach ($purchase->purchaseItems as $item) {
                    $item->product->decrement('stok', $item->quantity);
                }

                $purchase->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil dihapus dan stok telah dikurangi',
                ], 200);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pembelian tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pembelian',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
