<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
                aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('home') }}">
            <img src="{{ asset('argon') }}/img/brand/blue.png" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1-800x800.jpg">
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('My profile') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-calendar-grid-58"></i>
                        <span>{{ __('Activity') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-support-16"></i>
                        <span>{{ __('Support') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('argon') }}/img/brand/blue.png">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                                data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                                aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended"
                           placeholder="{{ __('Search') }}" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->
            <ul class="navbar-nav">
                @if(auth()->user()->hasPermissionGroup('dashboard'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::url() == route('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="ni ni ni-tv-2 {{ Request::url() == route('dashboard') ? 'text-secondary' : 'text-primary' }}"></i>
                            <span class="nav-link-text">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionGroup('turns'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::url() == route('turns') ? 'active' : '' }}"
                           href="{{ route('turns') }}">
                            <i class="ni ni-archive-2 {{ Request::url() == route('turns') ? 'text-secondary' : 'text-primary' }}"></i>
                            <span class="nav-link-text">{{ __('Turnos') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionGroup('settings'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::url() == route('settings') ? 'active' : '' }}"
                           href="{{ route('settings') }}">
                            <i class="ni ni-ui-04 {{ Request::url() == route('settings') ? 'text-secondary' : 'text-primary' }}"></i>
                            <span class="nav-link-text">{{ __('Configuracion') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionGroup('settings.users'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::url() == route('users') ? 'active' : '' }}" href="{{ route('users') }}">
                            <i class="far fa-user {{ Request::url() == route('users') ? 'text-secondary' : 'text-primary' }}"></i>
                            <span class="nav-link-text">{{ __('Usuarios') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionGroup('settings.roles'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::url() == route('roles') ? 'active' : '' }}" href="{{ route('roles') }}">
                            <i class="far fa-user-circle {{ Request::url() == route('roles') ? 'text-secondary' : 'text-primary' }}"></i>
                            <span class="nav-link-text">{{ __('Roles y Permisos') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
