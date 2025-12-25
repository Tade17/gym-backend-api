<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MobileClientController extends Controller
{
    public function getDashboard() {
        
    $user = Auth::user()->load(['subscriptions.plan', 'assignedRoutines' => function($q) {
        $q->where('assigned_date', now()->toDateString());
    }]);

    return response()->json([
        'user_name' => $user->first_name,
        'subscription_status' => $user->subscriptions->first()->status ?? 'inactive',
        'today_routine' => $user->assignedRoutines->first() ?? null,
    ]);
}
}
