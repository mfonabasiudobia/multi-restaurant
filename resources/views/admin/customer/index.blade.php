@extends('layouts.app')
@section('content')
    <div>
        <h4>{{ __('All Customers') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('Profile') }}</th>
                                <th style="min-width: 150px">{{ __('Name') }}</th>
                                <th style="min-width: 100px">{{ __('Phone') }}</th>
                                {{-- <th>{{ __('Email') }}</th> --}}
                                <th class="text-center">{{ __('Gender') }}</th>
                                <th class="text-center">{{ __('Date of Birth') }}</th>
                                <th class="text-center">{{ __('WhatsApp Notifications') }}</th>
                                <th class="text-center">{{ __('Change Password') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($customers as $key => $customer)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>
                                <td>
                                    <img src="{{ $customer->thumbnail }}" width="50" alt="{{ $customer->fullName }}">
                                </td>
                                <td>{{ Str::limit($customer->fullName, 50, '...') }}</td>
                                <td id="change-phone-{{ $customer->id }}">
                                    {{ $customer->phone ?? 'N/A' }}
                                    <button 
                                        class="btn btn-outline-secondary btn-sm ms-2" 
                                        onclick="showPhoneInput({{ $customer->id }})">
                                        <i class="fas fa-edit"></i> 
                                    </button>
                                </td>
                                {{-- <td>{{ $customer->email ?? 'N/A' }}</td> --}}
                                <td class="text-center">{{ $customer->gender ?? 'N/A' }}</td>
                                <td class="text-center">{{ $customer->date_of_birth ?? 'N/A' }}</td>
                                <td class="text-center" id="whatsapp-status-{{ $customer->id }}">
                                    @php
                                        $preference = $customer->notificationPreferences;
                                        $enabled = $preference ? $preference->whatsapp_enabled : false;
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span class="badge {{ $enabled ? 'bg-success' : 'bg-danger' }}">
                                            {{ $enabled ? __('Enabled') : __('Disabled') }}
                                        </span>
                                        <button 
                                            class="btn btn-sm {{ $enabled ? 'btn-danger' : 'btn-success' }}"
                                            onclick="toggleWhatsAppStatus({{ $customer->id }}, {{ $enabled ? 'false' : 'true' }})"
                                        >
                                            <i class="fas {{ $enabled ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div id="change-password-{{ $customer->id }}">
                                        <button 
                                            class="btn btn-outline-secondary btn-sm" 
                                            onclick="showPasswordInput({{ $customer->id }})">
                                            {{ __('Change Password') }}
                                        </button>
                                    </div>
                                </td>
                                {{-- <td class="text-center">
                                    <a href="{{ route('admin.customer.show', $customer->id) }}" class="btn btn-info btn-sm">
                                        {{ __('View') }}
                                    </a>
                                </td> --}}
                            <td class="text-center">
                                <button 
                                    class="btn btn-outline-danger btn-sm" 
                                    onclick="blockOrDeleteUser({{ $customer->id }}, '{{ $customer->is_active ? 'block' : 'unblock' }}')">
                                    <i class="fas {{ $customer->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                                <button 
                                    class="btn btn-outline-danger btn-sm ms-2" 
                                    onclick="blockOrDeleteUser({{ $customer->id }}, 'delete')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        function blockOrDeleteUser(customerId, action) {
            const message = action === 'block' ? 'block' : (action === 'unblock' ? 'unblock' : 'delete');
            if (confirm(`Are you sure you want to ${message} this user?`)) {
            $.ajax({
                url: '/api/block-or-delete-user/' + customerId,
                type: 'POST',
                data: { action: action },
                success: function(response) {
                console.log(response);
                if (response.success) {
                    alert(`User ${message}ed successfully.`);
                    location.reload();
                } else {
                    alert(response.message || `An error occurred while ${message}ing the user.`);
                }
                },
                error: function(error) {
                console.log(error);
                alert(`An error occurred while ${message}ing the user.`);
                }
            });
            }
        }

        function showPasswordInput(customerId) {
            const container = document.getElementById(`change-password-${customerId}`);
            container.innerHTML = `
                <input 
                    type="password" 
                    id="password-input-${customerId}" 
                    class="form-control d-inline w-50" 
                    placeholder="Enter new password"
                >
                <button 
                    class="btn btn-success btn-sm ms-2" 
                    onclick="changePassword(${customerId})">
                    {{ __('Save') }}
                </button>
                <button 
                    class="btn btn-secondary btn-sm ms-2" 
                    onclick="cancelChangePassword(${customerId})">
                    {{ __('Cancel') }}
                </button>
            `;
        }

        function cancelChangePassword(customerId) {
            const container = document.getElementById(`change-password-${customerId}`);
            container.innerHTML = `
                <button 
                    class="btn btn-outline-secondary btn-sm" 
                    onclick="showPasswordInput(${customerId})">
                    {{ __('Change Password') }}
                </button>
            `;
        }

        function changePassword(customerId) {
            const password = document.getElementById(`password-input-${customerId}`).value;

            if (!password) {
            alert("{{ __('Password cannot be empty.') }}");
            return;
            }

            if (password.length < 6) {
            alert("{{ __('Password must be at least 6 characters.') }}");
            return;
            }

            $.ajax({
            url: '/api/change-password/' + customerId,
            type: 'POST',
            data: { password: password },
            success: function(response) {
                console.log(response);
                if (response.success) {
                alert("{{ __('Password changed successfully.') }}");
                cancelChangePassword(customerId);
                } else {
                alert(response.message || "{{ __('An error occurred while changing the password.') }}");
                }
            },
            error: function(error) {
                console.log(error);
                alert("{{ __('An error occurred while changing the password.') }}");
            }
            });
        }
    function showPhoneInput(customerId) {
        const container = document.getElementById(`change-phone-${customerId}`);
        container.innerHTML = `
        <input 
            type="text" 
            id="phone-input-${customerId}" 
            class="form-control d-inline w-50" 
            placeholder="Enter new phone number"
        >
        <button 
            class="btn btn-success btn-sm ms-2" 
            onclick="changePhone(${customerId})">
            {{ __('Save') }}
        </button>
        <button 
            class="btn btn-secondary btn-sm ms-2" 
            onclick="cancelChangePhone(${customerId})">
            {{ __('Cancel') }}
        </button>
        `;
    }

    function cancelChangePhone(customerId) {
        const phone = document.getElementById(`phone-input-${customerId}`).value || '{{ $customer->phone ?? 'N/A' }}';
        const container = document.getElementById(`change-phone-${customerId}`);
        container.innerHTML = `
        ${phone}
        <button 
            class="btn btn-outline-secondary btn-sm ms-2" 
            onclick="showPhoneInput(${customerId})">
            <i class="fas fa-edit"></i> 
        </button>
        `;
    }

    function changePhone(customerId) {
        const phone = document.getElementById(`phone-input-${customerId}`).value;

        if (!phone) {
        alert("{{ __('Phone number cannot be empty.') }}");
        return;
        }

        $.ajax({
        url: '/api/change-phone-number/' + customerId,
        type: 'POST',
        data: { phone_number: phone },
        success: function(response) {
            console.log(response);
            if (response.success) {
            alert("{{ __('Phone number changed successfully.') }}");
            cancelChangePhone(customerId);
            } else {
            alert(response.message || "{{ __('An error occurred while changing the phone number.') }}");
            }
        },
        error: function(error) {
            console.log(error);
            alert("{{ __('An error occurred while changing the phone number.') }}");
        }
        });
    }

    function toggleWhatsAppStatus(customerId, enable) {
        if (confirm(`Are you sure you want to ${enable ? 'enable' : 'disable'} WhatsApp notifications for this user?`)) {
            $.ajax({
                url: `/admin/toggle-whatsapp-status/${customerId}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { whatsapp_enabled: enable },
                success: function(response) {
                    if (response.success) {
                        const container = document.getElementById(`whatsapp-status-${customerId}`);
                        container.innerHTML = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="badge ${enable ? 'bg-success' : 'bg-danger'}">
                                    ${enable ? '{{ __("Enabled") }}' : '{{ __("Disabled") }}'}
                                </span>
                                <button 
                                    class="btn btn-sm ${enable ? 'btn-danger' : 'btn-success'}"
                                    onclick="toggleWhatsAppStatus(${customerId}, ${!enable})"
                                >
                                    <i class="fas ${enable ? 'fa-toggle-off' : 'fa-toggle-on'}"></i>
                                </button>
                            </div>
                        `;
                        alert(`WhatsApp notifications ${enable ? 'enabled' : 'disabled'} successfully.`);
                    } else {
                        alert(response.message || `An error occurred while updating WhatsApp status.`);
                    }
                },
                error: function(error) {
                    console.log(error);
                    alert(`An error occurred while updating WhatsApp status.`);
                }
            });
        }
    }
    </script>
@endpush