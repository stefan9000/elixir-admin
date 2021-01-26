@extends('_layout.admin.master')
@section('content')
    <div class="index-performance">
        <h2>Elixir Performance</h2>

        <!-- red cards -->
        <div class="performance-row">
            <div class="p-card">
                <i class="fas fa-users"></i>
                <h6>Registered Users</h6>
                <h3>{{ $user_count }}</h3>
            </div>
            <div class="p-card">
                <i class="fas fa-ticket-alt"></i>
                <h6>Tickets Sold</h6>
                <h3>{{ $ticket_count }}</h3>
            </div>
            <div class="p-card">
                <i class="fas fa-globe-americas"></i>
                <h6>All Time Events</h6>
                <h3>{{ $event_count }}</h3>
            </div>
            <div class="p-card">
                <i class="fas fa-file"></i>
                <h6>All Time News</h6>
                <h3>{{ $news_count }}</h3>
            </div>
        </div>


        <!-- statistika -->
        <div class="stat-row">
            <!-- 1st -->
            <div class="stats">
                <div class="inner-stat">
                    <h6>Today</h6>
                    <h5>+{{ $user_count_today }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 7 Days</h6>
                    <h5>+{{ $user_count_week }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 30 Days</h6>
                    <h5>+{{ $user_count_month }}</h5>
                </div>
            </div>
            <!-- 2ndt -->
            <div class="stats">
                <div class="inner-stat">
                    <h6>Today</h6>
                    <h5>+{{ $ticket_count_today }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 7 Days</h6>
                    <h5>+{{ $ticket_count_week }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 30 Days</h6>
                    <h5>+{{ $ticket_count_month }}</h5>
                </div>
            </div>
            <!-- 3rd -->
            <div class="stats">
                <div class="inner-stat">
                    <h6>Today</h6>
                    <h5>+{{ $event_count_today }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 7 Days</h6>
                    <h5>+{{ $event_count_week }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 30 Days</h6>
                    <h5>+{{ $event_count_month }}</h5>
                </div>
            </div>
            <!-- 4th -->
            <div class="stats">
                <div class="inner-stat">
                    <h6>Today</h6>
                    <h5>+{{ $news_count_today }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 7 Days</h6>
                    <h5>+{{ $news_count_week }}</h5>
                </div>
                <div class="inner-stat">
                    <h6>Past 30 Days</h6>
                    <h5>+{{ $news_count_month }}</h5>
                </div>
            </div>

        </div>
    </div>
    @if ($latest_events->isNotEmpty())
        <div class="incoming-events">
            <h2>Incoming Events</h2>
                <div class="events-row">
                    @foreach($latest_events as $e)
                        <div class="event-card" onclick="window.location.href = '{{ route('admin_event_edit', ['event' => $e->id]) }}'">
                            <div class="e-card-content">
                                <div class="e-card-header" style="background-image: url('{{ $e->thumbnail }}');"></div>
                                <div class="icon-holder">
                                    <h4>{{ $e->starts_on->format('d') }}</h4>
                                    <h6>{{ $e->starts_on->format('M') }}</h6>
                                </div>
                                <div class="e-card-inner">
                                    <h4>{{ $e->getNameAttribute() }}</h4>
                                    <h5>{{ $e->artist }}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
    @endif

@endsection
