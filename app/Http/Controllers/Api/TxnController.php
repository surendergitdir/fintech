<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Transactions;
class TxnController extends Controller
{
    
    public function index()
    {
        if(auth()->user()->type ==0){
            $data = Transactions::paginate(10);
        }
        else{
            $data = Transactions::where('initiated_by',auth()->user()->id)->paginate(10);
        }
        if(!empty($data)){
            return response()->json([
                'success' => true,
                'message' => 'Get data successfully',
                'data' => $data->items(),
            ],200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'No Data Found!',
                'data' => null,
            ],400);
        }   
    }
    
    public function store(Request $request)
    {
        $rules = [
            'receiver_name'       => 'required|string|max:255',
            'receiver_account_no' => 'required|numeric|digits_between:10,16',
            'amount'              => 'required|numeric|min:0',
            'currency'            => 'required|in:USD,EUR,GBP',
            'sender_account_no'   => 'required|numeric|digits_between:10,16',
            'sender_name'         => 'required|string|max:255',
            'reference'           => 'required|string'
        ];
        $message = [
            'currency.in' => 'Currency supported only USD,EUR,GBP'
        ];
        $user = auth()->user();
        $validator = Validator::make($request->all(), $rules,$message);
        if ($validator->fails()) {
            return response()->json(['message'=>'Bad Request','errors' => $validator->errors()],400); 
        }
        DB::beginTransaction();
        try{
            $txn_data = Transactions::create([
                'receiver_name'      => $request->receiver_name,
                'receiver_account_no'=> $request->receiver_account_no,
                'amount'             => $request->amount,
                'currency'           => $request->currency,
                'sender_account_no'  => $request->sender_account_no,
                'sender_name'        => $request->sender_name,
                'reference'          => $request->reference,
                'initiated_by'       => $user->id,
                'txn_id'             => createStr().'-'.mt_rand(1000,9999).'-'.createStr().'-'.mt_rand(1000,9999)
            ]);
            DB::commit();
            unset($txn_data['updated_at']);
            return response()->json([
                'success' => true,
                'message' => 'Txn created successfully',
                'data' => $txn_data,
            ],201);
        }
        catch(\Exception $err){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'data' => null
            ],500);
        }
    }

    public function update(Request $request,$id)
    {

        //check if transaction is valid 
        $checkTxn = Transactions::find($id);
        if (!$checkTxn) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 400);
        }
        //check if transaction is initiated by same user 
        if($checkTxn->initiated_by != auth()->user()->id){
            return response()->json([
                'success' => false,
                'message' => 'Invalid Transaction id'
            ], 400);
        }
        $rules = [
            'receiver_name'       => 'required|string|max:255',
            'receiver_account_no' => 'required|numeric|digits_between:10,16',
            'amount'              => 'required|numeric|min:0',
            'currency'            => 'required|in:USD,EUR,GBP',
            'sender_account_no'   => 'required|numeric|digits_between:10,16',
            'sender_name'         => 'required|string|max:255',
            'reference'           => 'required|string'
        ];
        $message = [
            'currency.in' => 'Currency supported only USD,EUR,GBP'
        ];
        $validator = Validator::make($request->all(), $rules,$message);
        if ($validator->fails()) {
            return response()->json(['message'=>'Bad Request','errors' => $validator->errors()],400); 
        }
        DB::beginTransaction();
        try{ 
            $checkTxn->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Txn Updated successfully',
                'data' => $checkTxn,
            ],201);
        }
        catch(\Exception $err){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'data' => null
            ],500);
        }
    }


    public function destroy(string $id)
    {
        //check if Transaction available
        $checkTxn = Transactions::find($id);
        if (!$checkTxn) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 400);
        }
        //check if transaction is initiated by same user 
        if(auth()->user()->type == 0){
            $checkTxn->delete();
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
