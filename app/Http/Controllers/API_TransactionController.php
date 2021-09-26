<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class API_TransactionController extends Controller
{
    public function getTransaction()
    {
        $transaction = Transaction::get();
        return $transaction;
    }

    public function getTransactionId($id = null) {
        if (empty($id)) {
            $transaction = Transaction::get();
            return response()->json(["transaction" => $transaction], 200);
        } else {
            $transaction = Transaction::find($id);
            return response()->json(["transaction" => $transaction], 200);
        }
    }

    public function addTransaction(Request $request)
    {
        if($request->isMethod('post')) {
            $transactionData = $request->input();

            // Advance Post API Validations
            $rules = [
                "id_customer" => "required",
                "id_product" => "required",
                "date" => "required",
                "total" => "required",
                "payment" => "required",
            ];

            $customMessage = [
                'id_customer.required' => 'id_customer is required',
                'id_product.required' => 'id_product is required',
                'date.required' => 'date is required',
                'total.required' => 'total is required',
                "payment.required" => 'payment is required',
            ];

            $validator = Validator::make($transactionData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                $transaction = new Transaction;
                $transaction->id_customer = $transactionData['id_customer'];
                $transaction->id_product = $transactionData['id_product'];
                $transaction->date = $transactionData['date'];
                $transaction->total = $transactionData['total'];
                $transaction->payment = $transactionData['payment'];
                $transaction->refund = $transaction->payment - $transaction->total;
                $transaction->save();
                return response()->json(['message' => 'Transaction added successfully!'], 201);
            }
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        if ($request->isMethod('PUT')) {
            $transactionData = $request->input();

            $rules = [
                "id_customer" => "required",
                "id_product" => "required",
                "date" => "required",
                "total" => "required",
                "payment" => "required",
            ];

            $customMessage = [
                'id_customer.required' => 'id_customer is required',
                'id_product.required' => 'id_product is required',
                'date.required' => 'date is required',
                'total.required' => 'total is required',
                "payment.required" => 'payment is required',
            ];

            $validator = Validator::make($transactionData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                Transaction::where('id', $id)->update([
                    "id_customer" => $transactionData['id_customer'],
                    "id_product" => $transactionData['id_product'],
                    "date" => $transactionData['date'],
                    "total" => $transactionData['total'],
                    "payment" => $transactionData['payment'],
                    "refund" => $transactionData['payment'] - $transactionData['total'],
                ]);
                return response()->json(['message' => 'Transaction updated successfully!'], 202);
            }
        }
    }

    public function deleteTransaction($id)
    {
        Transaction::where('id', $id)->delete();
        return response()->json(['message' => 'Transaction deleted successfully'], 202);
    }
}
