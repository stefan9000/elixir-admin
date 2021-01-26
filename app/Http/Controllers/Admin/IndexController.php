<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\Http\Controllers\Controller;
use App\News;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IndexController extends Controller
{
    /**
     * Shows the admin index page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user_count = User::where('type', User::REGULAR_USER)
            ->count();
        $user_count_today = User::where('type', User::REGULAR_USER)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = "' . Carbon::now()->format('Y-m-d') . '"')
            ->count();
        $user_count_week = User::where('type', User::REGULAR_USER)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subWeeks(1)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();
        $user_count_month = User::where('type', User::REGULAR_USER)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subDays(30)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();

        $event_count = Event::count();
        $event_count_today = Event::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = "' . Carbon::now()->format('Y-m-d') . '"')
            ->count();
        $event_count_week = Event::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subWeeks(1)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();
        $event_count_month = Event::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subDays(30)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();

        $ticket_count = Ticket::count();
        $ticket_count_today = Ticket::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = "' . Carbon::now()->format('Y-m-d') . '"')
            ->count();
        $ticket_count_week = Ticket::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subWeeks(1)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();
        $ticket_count_month = Ticket::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subDays(30)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();

        $news_count = News::count();
        $news_count_today = News::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = "' . Carbon::now()->format('Y-m-d') . '"')
            ->count();
        $news_count_week = News::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subWeeks(1)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();
        $news_count_month = News::whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . Carbon::now()->subDays(30)->format('Y-m-d') . '"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "'. Carbon::now()->format('Y-m-d') .'"')
            ->count();

        $latest_events = Event::latest()->limit(4)->get();

        return view('admin.index', compact([
            'user_count',
            'user_count_today',
            'user_count_week',
            'user_count_month',
            'event_count',
            'event_count_today',
            'event_count_week',
            'event_count_month',
            'ticket_count',
            'ticket_count_today',
            'ticket_count_week',
            'ticket_count_month',
            'news_count',
            'news_count_today',
            'news_count_week',
            'news_count_month',
            'latest_events',
        ]));
    }
}
