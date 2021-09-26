<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class API_CustomerController extends Controller
{
    public function getCustomer()
    {
        $customers = Customer::get();
        return $customers;
    }

    public function getCustomerId($id = null)
    {
        if (empty($id)) {
            $customers = Customer::get();
            return response()->json(["customers" => $customers], 200);
        } else {
            $customers = Customer::find($id);
            return response()->json(["customers" => $customers], 200);
        }
    }

    public function addCustomer(Request $request) {
        if($request->isMethod('post')) {
            $customerData = $request->input();

            // Advance Post API Validations
            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                "start_join" => "required",
                "expired_join" => "required",
                "is_active" => "required",
            ];

            $customMessage = [
                'name.required' => 'Name is required',
                'start_join.required' => 'start_join is required',
                'expired_join.required' => 'expired_join is required',
                'is_active.required' => 'is_active is required',
            ];

            $validator = Validator::make($customerData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $customer = new Customer;
            $customer->name = $customerData['name'];
            $customer->start_join = $customerData['start_join'];
            $customer->expired_join = $customerData['expired_join'];
            $customer->is_active = $customerData['is_active'];
            $customer->save();
            return response()->json(['message' => 'Customer added successfully!'], 201);
        }
    }

    public function updateCustomer(Request $request, $id)
    {
        if ($request->isMethod('PUT')) {
            $customerData = $request->input();

            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                "start_join" => "required",
                "expired_join" => "required",
                "is_active" => "required",
            ];

            $customMessage = [
                'name.required' => 'Name is required',
                'start_join.required' => 'start_join is required',
                'expired_join.required' => 'expired_join is required',
                'is_active.required' => 'is_active is required',
            ];

            $validator = Validator::make($customerData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                Customer::where('id', $id)->update([
                    "name" => $customerData['name'],
                    "start_join" => $customerData['start_join'],
                    "expired_join" => $customerData['expired_join'],
                    "is_active" => $customerData['is_active'],
                ]);
                return response()->json(['message' => 'Customer updated successfully!'], 202);
            }
        }
    }

    public function deleteCustomer($id)
    {
        Customer::where('id', $id)->delete();
        return response()->json(['message' => 'Customer deleted successfully'], 202);
    }
}
