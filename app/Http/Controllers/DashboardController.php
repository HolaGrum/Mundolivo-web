<?php

namespace App\Http\Controllers;

use Vanilo\Product\Models\Product;
use Vanilo\Order\Models\Order;
use Vanilo\Channel\Models\Channel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        return view('admin.dashboard', [
            'user' => $user,
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'userCount' => User::count(),
            'channelCount' => Channel::count(),
            'pendingOrders' => Order::where('status', 'open')->count(),
            'processingOrders' => Order::where('status', 'processing')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
        ]);
    }
}
