<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json(['data' => $customers]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $customer = Customer::create($validator->validated());
            return response()->json(['data' => $customer, 'message' => 'Customer created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating customer', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json(['data' => $customer]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $customer->update($validator->validated());
            return response()->json(['data' => $customer, 'message' => 'Customer updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating customer', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting customer', 'error' => $e->getMessage()], 500);
        }
    }
}