<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebNotificationResource;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // fetch notifications for admin
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(10);

        return view('shop.notifications.index', compact('notifications'));
    }

    // mark as read
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    // show notification list
    public function show()
    {
        $shop = generaleSetting('shop');
        $notifications = NotificationRepository::query()->where('shop_id', $shop->id)->orderBy('is_read', 'asc')->latest('id')->paginate(20);

        return view('shop.notification', compact('notifications'));
    }

    // mark all as read
    public function markAllAsRead()
    {
        $shop = generaleSetting('shop');
        NotificationRepository::query()->where('shop_id', $shop->id)->update(['is_read' => true]);

        return back()->withSuccess(__('All notifications marked as read!'));
    }

    // destroy notification
    public function destroy(Notification $notification)
    {

        $notification->delete();

        return back()->withSuccess(__('Notification deleted!'));
    }
}
