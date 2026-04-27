<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'cedula'         => 'required|string|max:20|unique:affiliates',
            'whatsapp'       => 'required|string|max:20',
            'email'          => 'required|email|unique:affiliates',
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:30',
            'account_type'   => 'in:ahorros,corriente',
            'description'    => 'required|string|max:1000',
        ]);

        $affiliate = Affiliate::create([
            ...$data,
            'user_id' => $request->user()?->id,
            'status'  => 'pending',
        ]);

        if ($request->user() && $request->user()->role !== 'affiliate') {
            $request->user()->update(['role' => 'affiliate']);
        }

        return response()->json(['data' => $affiliate, 'message' => 'Solicitud enviada correctamente.'], 201);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $affiliate = Affiliate::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->with(['commissions', 'referrals'])
            ->first();

        if (! $affiliate || $affiliate->status !== 'approved') {
            return response()->json(['data' => null, 'status' => $affiliate?->status ?? 'not_found']);
        }

        $data = [
            'referral_code'        => $affiliate->referral_code,
            'total_referrals'      => $affiliate->referrals()->count(),
            'converted_sales'      => $affiliate->referrals()->where('status', 'converted')->count(),
            'pending_commissions'  => $affiliate->commissions()->where('status', 'pending')->sum('amount'),
            'paid_commissions'     => $affiliate->commissions()->where('status', 'paid')->sum('amount'),
            'commissions'          => $affiliate->commissions()->latest()->take(10)->get(),
        ];

        return response()->json($data);
    }

    public function commissions(Request $request)
    {
        $user = $request->user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();

        if (! $affiliate) {
            return response()->json(['data' => []]);
        }

        $commissions = $affiliate->commissions()->with('referral')->latest()->paginate(15);
        return response()->json($commissions);
    }
}
