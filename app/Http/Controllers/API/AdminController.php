<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Commission;
use App\Models\Contact;
use App\Models\Property;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_properties' => Property::count(),
            'total_users'      => User::count(),
            'total_affiliates' => Affiliate::count(),
            'pending_affiliates' => Affiliate::where('status', 'pending')->count(),
            'total_sales'      => Referral::where('status', 'converted')->count(),
            'total_contacts'   => Contact::count(),
            'unread_contacts'  => Contact::where('read', false)->count(),
            'pending_commissions' => Commission::where('status', 'pending')->sum('amount'),
            'paid_commissions' => Commission::where('status', 'paid')->sum('amount'),
        ]);
    }

    public function affiliates(Request $request)
    {
        $query = Affiliate::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function updateAffiliateStatus(Request $request, Affiliate $affiliate)
    {
        $data = $request->validate([
            'status'          => 'required|in:approved,rejected',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $affiliate->update($data);

        if ($data['status'] === 'approved' && $affiliate->user) {
            $affiliate->user->update(['role' => 'affiliate']);
        }

        return response()->json(['data' => $affiliate]);
    }

    public function createSale(Request $request)
    {
        $data = $request->validate([
            'affiliate_id'  => 'required|exists:affiliates,id',
            'property_id'   => 'required|exists:properties,id',
            'client_id'     => 'nullable|exists:users,id',
            'sale_amount'   => 'required|numeric|min:0',
        ]);

        $affiliate = Affiliate::findOrFail($data['affiliate_id']);

        $referral = Referral::create([
            'affiliate_id' => $affiliate->id,
            'client_id'    => $data['client_id'] ?? null,
            'property_id'  => $data['property_id'],
            'status'       => 'converted',
        ]);

        $commissionAmount = ($data['sale_amount'] * $affiliate->commission_rate) / 100;

        $commission = Commission::create([
            'affiliate_id'    => $affiliate->id,
            'referral_id'     => $referral->id,
            'amount'          => $commissionAmount,
            'commission_rate' => $affiliate->commission_rate,
            'status'          => 'pending',
        ]);

        Property::find($data['property_id'])?->update(['status' => 'vendido']);

        return response()->json(['data' => compact('referral', 'commission')], 201);
    }

    public function commissions(Request $request)
    {
        $query = Commission::with(['affiliate.user', 'referral.property']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function approveCommission(Commission $commission)
    {
        $commission->update(['status' => 'approved']);
        return response()->json(['data' => $commission]);
    }

    public function markCommissionPaid(Commission $commission)
    {
        $commission->update(['status' => 'paid', 'paid_at' => now()]);
        return response()->json(['data' => $commission]);
    }
}
