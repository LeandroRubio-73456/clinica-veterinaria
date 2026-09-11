<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request, NotificationService $notifications): View
    {
        return view('notifications.index', [
            'notifications' => $notifications->for($request->user()),
        ]);
    }

    public function live(Request $request, NotificationService $notifications)
    {
        $all = $notifications->for($request->user());

        return response()->json([
            'count' => $all->count(),
            'items' => $all->take(5)->map(fn (array $notification) => [
                'title' => $notification['title'],
                'message' => $notification['message'],
                'url' => $notification['url'],
                'priority' => $notification['priority'],
            ])->values(),
        ]);
    }
}
