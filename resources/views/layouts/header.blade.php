<div class="app-header">
    <div class="app-header-inner">
        <div class="app-header-left">
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
        <div class="app-header-right">
            @if ($businessModel == 'multi')
                @php
                    $user = auth()->user();
                    $isShop = true;
                    if (!$user->hasRole('root') && ($user->shop || $user->myShop)) {
                        $isShop = false;
                    }
                @endphp
            @endif

            @hasPermission(['admin.dashboard.notification', 'shop.dashboard.notification'])
                <div class="badgeButtonBox me-3">
                    <div class="notifactionIcon">
                        <button type="button" class="emailBadge dropdown-toggle position-relative"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell noti"></i>
                            <span class="position-absolute notificationCount" id="totalNotify"></span>
                        </button>
                        <div class="dropdown-menu p-0 emailNotifactionSection">
                            <div class="dropdown-item emailNotifaction">
                                <div class="emailHeader">
                                    <h6 class="massTitel">
                                        {{ __('Notifications') }}
                                    </h6>
                                    <a href="@hasPermission('admin.dashboard.notification')
                                            {{ route('admin.notification.readAll') }}
                                        @else
                                            {{ route('shop.notification.readAll') }}
                                        @endhasPermission"
                                        class="text-dark">
                                        {{ __('Marks all as read') }}
                                    </a>
                                </div>
                                <div class="emailBody" id="notifications">
                                    <!-- Notifications will be loaded here -->
                                </div>
                                <div class="emailFooter">
                                    <a href="@hasPermission('admin.dashboard.notification')
                                            {{ route('admin.notification.show') }}
                                        @else
                                            {{ route('shop.notification.show') }}
                                        @endhasPermission">
                                        {{ __('View All') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasPermission

            <div class="header-btn-lg pe-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img width="42" class="rounded-circle" src="{{ auth()->user()->profile_photo }}" alt="">
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ request()->is('admin/*', 'admin') ? route('admin.profile.index') : route('shop.profile.index') }}" 
                                       tabindex="0" class="dropdown-item">
                                        {{ __('Profile') }}
                                    </a>
                                    <button type="button" tabindex="0" class="dropdown-item logout">
                                        {{ __('Logout') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 