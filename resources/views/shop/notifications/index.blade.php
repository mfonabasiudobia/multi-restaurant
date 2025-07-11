@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Notifications') }}</h4>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Message') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $notification->data['message'] ?? '' }}</td>
                                            <td>
                                                @if(!$notification->read_at)
                                                    <button class="btn btn-sm btn-primary mark-as-read" 
                                                            data-id="{{ $notification->id }}">
                                                        {{ __('Mark as Read') }}
                                                    </button>
                                                @else
                                                    <span class="badge bg-success">{{ __('Read') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $notifications->links() }}
                    @else
                        <p class="text-center">{{ __('No notifications found') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.mark-as-read').forEach(button => {
    button.addEventListener('click', function() {
        const notificationId = this.dataset.id;
        axios.post(`/shop/notifications/${notificationId}/mark-as-read`)
            .then(response => {
                if (response.data.success) {
                    this.parentElement.innerHTML = '<span class="badge bg-success">{{ __("Read") }}</span>';
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    });
});
</script>
@endpush 