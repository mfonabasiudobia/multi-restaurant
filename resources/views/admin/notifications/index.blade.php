@extends('layouts.app')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div>{{ __('Notifications') }}
                <div class="page-title-subheading">{{ __('Manage notifications') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Message') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>{{ $notification->data['title'] ?? '' }}</td>
                            <td>{{ $notification->data['message'] ?? '' }}</td>
                            <td>{{ $notification->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <span class="badge {{ $notification->read_at ? 'bg-success' : 'bg-warning' }}">
                                    {{ $notification->read_at ? __('Read') : __('Unread') }}
                                </span>
                            </td>
                            <td>
                                @if(!$notification->read_at)
                                    <form action="{{ route('admin.notifications.mark-as-read', $notification->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fa fa-check"></i> {{ __('Mark as Read') }}
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this notification?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No notifications found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@if(count($notifications) > 0)
    <div class="mt-3">
        <form action="{{ route('admin.notifications.mark-all-as-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fa fa-check-double"></i> {{ __('Mark All as Read') }}
            </button>
        </form>

        <form action="{{ route('admin.notifications.delete-all') }}" method="POST" class="d-inline"
              onsubmit="return confirm('{{ __('Are you sure you want to delete all notifications?') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fa fa-trash"></i> {{ __('Delete All') }}
            </button>
        </form>
    </div>
@endif
@endsection 