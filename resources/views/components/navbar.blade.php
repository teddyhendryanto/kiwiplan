<nav class="navbar navbar-default navbar-fixed-top">
  <div class="brand">
    <a href="{{ route('home') }}"><img src="{{ asset('img/'.env('SITE_ASSET').'/logo-text.png')}}" alt="{{ env('SITE_NAME') }}" class="img-responsive logo"></a>
  </div>
  <div class="container-fluid">
    <div class="navbar-btn">
      <button type="button" class="btn-toggle-fullwidth">
        <i class="fa fa-bars"></i>
      </button>
    </div>
    <!--<form class="navbar-form navbar-left">
      <div class="input-group">
        <input type="text" value="" class="form-control" placeholder="Search dashboard...">
        <span class="input-group-btn"><button type="button" class="btn btn-primary">Go</button></span>
      </div>
    </form>-->
    <div class="navbar-btn navbar-btn-right">
    </div>
    <div id="navbar-menu">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
            <i class="fa fa-bell"></i>
            @if (count(Auth::user()->unreadNotifications))
              <span class="badge bg-danger">{{ count(Auth::user()->unreadNotifications) }}</span>
            @endif
          </a>
          @if (count(Auth::user()->unreadNotifications) <= 0)
            <ul class="dropdown-menu notify-drop no-notification">
              <li> <a href="#">Tidak ada notifikasi</a> </li>
            </ul>
          @else
            <ul class="dropdown-menu notifications">
              <div class="notifications-scroll">
                <li onclick="markAllAsRead()">
                  <a href="#" class="more">Baca Semua</a>
                </li>
                @foreach (Auth::user()->unreadNotifications as $notification)
                  <li onclick="markAsRead('{{$notification->id}}');">
                    @include('partials.notifications.'.snake_case(class_basename($notification->type)))
                  </li>
                @endforeach
                <!-- notify content -->
                <li><a href="{{ route('notif.index','unread') }}" class="more">Lihat Semua</a></li>
              </div>
            </ul>
          @endif
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span>{{ Auth::user()->name }}</span> <i class="icon-submenu fa fa-angle-down"></i>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                <i class="lnr lnr-exit"></i> <span>Logout</span>
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
              </form>
            </li>
          </ul>
        </li>
        <!-- <li>
          <a class="update-pro" href="https://www.themeineed.com/downloads/klorofil-pro-bootstrap-admin-dashboard-template/?utm_source=klorofil&utm_medium=template&utm_campaign=KlorofilPro" title="Upgrade to Pro" target="_blank"><i class="fa fa-rocket"></i> <span>UPGRADE TO PRO</span></a>
        </li> -->
      </ul>
    </div>
  </div>
</nav>
