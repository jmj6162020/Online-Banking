<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'cartItemCount' => $this->getCartItemCount(),
            'refundRequests' => RefundRequest::all(),
        ];
    }

    private function getCartItemCount()
    {
        $userId = Auth::id();
        $guestId = Session::get('guest_id');

        return CartItem::where(function ($query) use ($userId, $guestId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_id', $guestId);
            }
        })
            ->count();
    }
}
