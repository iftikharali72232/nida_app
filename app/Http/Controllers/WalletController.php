<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $data['perPage'] = 10;
        $data['wallets'] = Wallet::with('user')->orderByDesc('id')->paginate($data['perPage']);
        return view('wallet.index', $data);
    }

    public function edit($id)
    {
        $wallet = Wallet::find($id);
        $user = User::find($wallet->user_id);
        return view('wallet.edit', compact('user', 'wallet'));
    }

    public function update($id, Request $request)
    {
        $amount = doubleval($request->input('amount'));
        $wallet = Wallet::find($id);
        $user = User::find($wallet->user_id);

        if ($request->has('recharge') && $request->input('recharge') == 1) {
            $newAmount = $amount + $wallet->amount;

            $wallet->update(['amount' => $newAmount]);

            WalletHistory::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'is_deposite' => 1,
                'description' => $amount. " points successfully added in your voucher.",
            ]);
            if($user)
            {
                $data = [
                    'user_id' => $user->id,
                    'text_en' => $amount. " points successfully added in your voucher.",
                    'text_ar' => "تمت إضافة $amount نقاط بنجاح إلى قسيمتك.",
                    'request_id' => $wallet->id,
                    'page' => $request->page
                ];
                storeNotification($data);
                $datap = [
                    'is_user' => 1,
                    'device_token' => $user->device_token,
                    'title' => 'Voucher Update',
                    'body' => $data['text_en'],
                    'request_id' => $data['request_id']
                ];
                sendNotification($datap);
            }
        } elseif ($request->has('withdraw') && $request->input('withdraw') == 1) {
            $newAmount = $wallet->amount - $amount;

            $wallet->update(['amount' => $newAmount]);

            WalletHistory::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'is_expanse' => 1,
                'description' => $amount. " points successfully withdrawn.",
            ]);
            if($user)
            {
                $data = [
                    'user_id' => $user->id,
                    'text_en' => $amount. " points successfully withdrawn.",
                    'text_ar' => "تم سحب $amount نقاط بنجاح.",
                    'request_id' => $wallet->id,
                    'page' => $request->page
                ];
                storeNotification($data);
                $datap = [
                    'is_user' => 0,
                    'device_token' => $user->device_token,
                    'title' => 'Voucher Update',
                    'body' => $data['text_en'],
                    'request_id' => $data['request_id']
                ];
                sendNotification($datap);
            }
        }

        return redirect()->route('wallet.index')->with('success', trans('lang.update_message'));
    }
    public function history(Request $request, $id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);
        
        $query = WalletHistory::where('wallet_id', $id)
                    ->with('service')
                    ->orderBy('created_at', 'desc');
    
        // Apply date range filter if provided
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $history = $query->paginate(10);
    
        // Calculate totals for credit and debit
        $totalCredit = WalletHistory::where('wallet_id', $id)
                        ->when($request->start_date && $request->end_date, function($query) use ($request) {
                            $query->whereBetween('created_at', [
                                Carbon::parse($request->start_date)->startOfDay(),
                                Carbon::parse($request->end_date)->endOfDay()
                            ]);
                        })
                        ->where('is_deposite', 1)
                        ->sum('amount');
    
        $totalDebit = WalletHistory::where('wallet_id', $id)
                        ->when($request->start_date && $request->end_date, function($query) use ($request) {
                            $query->whereBetween('created_at', [
                                Carbon::parse($request->start_date)->startOfDay(),
                                Carbon::parse($request->end_date)->endOfDay()
                            ]);
                        })
                        ->where('is_expanse', 1)
                        ->sum('amount');
    
        return view('wallet.history', compact('wallet', 'history', 'totalCredit', 'totalDebit'));
    }
    

}
