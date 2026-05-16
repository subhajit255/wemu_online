<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!-- Logo -->
    <div class="app-sidebar-logo px-6 sideHead" id="kt_app_sidebar_logo" style="border-bottom: 1px solid #f3f4f6;">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center" style="text-decoration: none;">
            <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                <span class="text-white fw-bold fs-4" style="line-height: 1;">W</span>
            </div>
            <h3 class="app-sidebar-logo-default m-0 ms-3 fw-bolder" style="font-size: 22px; color: #111827; letter-spacing: -0.5px;">Wemu</h3>
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <span class="svg-icon svg-icon-2 rotate-180">
                <i class="fa-sharp fa-solid fa-arrow-right-arrow-left"></i>
            </span>
        </div>
    </div>

    <!-- Menu -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                <!-- Dashboard -->
                <div class="menu-item">
                    <a href="{{ auth()->user() && auth()->user()->user_type == 3 ? route('artist.dashboard') : route('admin.dashboard') }}">
                        <span class="menu-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="fa-solid fa-border-all fs-5"></i></span>
                            <span class="menu-title">Dashboard</span>
                        </span>
                    </a>
                </div>

                <!-- Music -->
                <div class="menu-item">
                    <a href="{{ auth()->user() && auth()->user()->user_type == 3 ? route('artist.songs.index') : '#' }}">
                        <span class="menu-link {{ request()->routeIs('artist.songs.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="fa-solid fa-music fs-5"></i></span>
                            <span class="menu-title">Music</span>
                        </span>
                    </a>
                </div>

                <!-- Albums -->
                <div class="menu-item">
                    <a href="{{ auth()->user() && auth()->user()->user_type == 3 ? route('artist.albums.index') : '#' }}">
                        <span class="menu-link {{ request()->routeIs('artist.albums.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="fa-solid fa-compact-disc fs-5"></i></span>
                            <span class="menu-title">Albums</span>
                        </span>
                    </a>
                </div>

                <!-- Analytics -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-chart-line fs-5"></i></span>
                            <span class="menu-title">Analytics</span>
                        </span>
                    </a>
                </div>

                <!-- Audience -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-users fs-5"></i></span>
                            <span class="menu-title">Audience</span>
                        </span>
                    </a>
                </div>

                <!-- Royalties -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-sack-dollar fs-5"></i></span>
                            <span class="menu-title">Royalties</span>
                        </span>
                    </a>
                </div>

                <!-- Promotion -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-bullhorn fs-5"></i></span>
                            <span class="menu-title">Promotion</span>
                        </span>
                    </a>
                </div>

                <!-- Releases -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-calendar-check fs-5"></i></span>
                            <span class="menu-title">Releases</span>
                        </span>
                    </a>
                </div>

                <!-- Messages -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-envelope fs-5"></i></span>
                            <span class="menu-title">Messages</span>
                        </span>
                    </a>
                </div>

                <!-- Team -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-user-group fs-5"></i></span>
                            <span class="menu-title">Team</span>
                        </span>
                    </a>
                </div>

                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7 text-muted">System</span>
                    </div>
                </div>

                <!-- Settings -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-gear fs-5"></i></span>
                            <span class="menu-title">Settings</span>
                        </span>
                    </a>
                </div>

                <!-- Support -->
                <div class="menu-item">
                    <a href="#">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-life-ring fs-5"></i></span>
                            <span class="menu-title">Support</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Profile -->
    <div class="app-sidebar-footer d-flex flex-column flex-center w-100" id="kt_app_sidebar_footer" style="padding: 15px; border-top: 1px solid #f3f4f6; background: #ffffff;">
        <div class="d-flex align-items-center w-100" style="padding: 10px; border-radius: 8px;">
            <div class="avatar avatar-md avatar-circle me-3">
                <img style="border-radius: 50%; width: 45px; height: 45px; object-fit: cover;" class="onerror-image"
                    data-onerror-image="{{ auth()->user()->image_path }}"
                    src="{{ auth()->user()->image_path }}" alt="Avatar">
            </div>
            <div class="d-flex flex-column flex-grow-1">
                <span class="fw-bold text-dark fs-6">
                    @if (auth()->user()->name)
                    {{ auth()->user()->name }}
                    @else
                    The Weeknd
                    @endif
                </span>
                {{-- <a href="#" class="text-muted text-hover-primary fs-8 fw-semibold">View Profile</a> --}}
                <a href="javascript:void(0)" class="text-muted text-hover-primary fs-8 fw-semibold" data-bs-toggle="modal" data-bs-target="#admin_update_form">View Profile</a>
            </div>
            <div class="cursor-pointer">
                <a action="{{ route('admin.logout') }}" class='logOut' title="Logout">
                    <i class="fa fa-sign-out" aria-hidden="true" style="font-size: 1.25rem; color: #9ca3af;"></i>
                </a>
            </div>
        </div>
    </div>
</div>