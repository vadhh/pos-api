<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = Sale::with(['customer', 'items.product'])->get();
        return response()->json(['data' => $sales]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.discount_amount' => 'numeric|min:0',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return DB::transaction(function () use ($request) {
                $total_amount = 0;
                $total_discount = 0;

                // Calculate totals and validate stock
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    $item_total = $product->price * $item['quantity'];
                    $discount = $item['discount_amount'] ?? 0;
                    $total_amount += $item_total;
                    $total_discount += $discount;
                }

                $final_amount = $total_amount - $total_discount;

                // Create sale record
                $sale = Sale::create([
                    'customer_id' => $request->customer_id,
                    'total_amount' => $total_amount,
                    'discount_amount' => $total_discount,
                    'final_amount' => $final_amount,
                    'payment_status' => 'completed',
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes
                ]);

                // Create sale items and update stock
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit_price = $product->price;
                    $quantity = $item['quantity'];
                    $discount = $item['discount_amount'] ?? 0;
                    $final_amount = ($unit_price * $quantity) - $discount;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'discount_amount' => $discount,
                        'final_amount' => $final_amount
                    ]);

                    // Update product stock
                    $product->decrement('stock', $quantity);
                }

                return response()->json([
                    'data' => Sale::with(['customer', 'items.product'])->find($sale->id),
                    'message' => 'Sale created successfully'
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating sale', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'items.product']);
        return response()->json(['data' => $sale]);
    }

    public function updatePaymentStatus(Request $request, Sale $sale): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_status' => 'required|string|in:pending,completed,cancelled',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $sale->update([
                'payment_status' => $request->payment_status,
                'notes' => $request->notes ?? $sale->notes
            ]);

            return response()->json([
                'data' => $sale->fresh(),
                'message' => 'Payment status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating payment status', 'error' => $e->getMessage()], 500);
        }
    }
}