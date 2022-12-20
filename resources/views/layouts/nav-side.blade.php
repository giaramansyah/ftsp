<aside class="main-sidebar sidebar-light-lightblue elevation-1">
  <a href="index3.html" class="brand-link">
    <img src="{{ asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle"
      style="opacity: .8">
    <span class="brand-text font-weight-light">MOKU FTSP</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent" data-widget="treeview" role="menu"
        data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('home') }}" class="nav-link{{ Route::currentRouteName() == 'home' ? ' active' : '' }}">
            <i class="nav-icon fas fa-home"></i>
            <p>
              {{ config('global.home') }}
            </p>
          </a>
        </li>
        @foreach ($side_nav as $value)
          <li class="nav-item{{ $value['active'] ? ' menu-open' : '' }}">
            <a href="#" class="nav-link{{ $value['active'] ? ' active' : '' }}">
              <i class="nav-icon fas {{ $value['icon'] }}"></i>
              <p>
                {{ __($value['label']) }}
                <i class="right fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @foreach ($value['menus'] as $val)
                <li class="nav-item">
                  <a href="{{ route($val['url']) }}" class="nav-link{{ $val['active'] ? ' active' : '' }}">
                    <i class="fas fa-diamond nav-icon"></i>
                    <p>{{ __($val['label']) }}</p>
                  </a>
                </li>
              @endforeach
            </ul>
          </li>
        @endforeach
      </ul>
    </nav>
  </div>
</aside>