<div id="sidebar-nav" class="sidebar">
  <div class="sidebar-scroll">
    <nav>
      <ul class="nav">
        <li><a href="{{ route('home') }}">
          <span>Dashboard</span></a>
        </li>
        @role('superuser')
        <li>
          <a href="#users" data-toggle="collapse" class="collapsed">
            <span>User</span>
            <i class="icon-submenu lnr lnr-chevron-left"></i>
          </a>
          <div id="users" class="collapse ">
            <ul class="nav">
              <li><a href="{{ route('permissions.index') }}" ><span>Permission</span></a></li>
              <li><a href="{{ route('roles.index') }}" ><span>Role</span></a></li>
              <li><a href="{{ route('users.index') }}" ><span>User</span></a></li>
            </ul>
          </div>
        </li>
        @endrole
        @permission('accounting-access')
        <li>
          <a href="#accounting" data-toggle="collapse" class="collapsed">
            <span>Accounting</span>
            <i class="icon-submenu lnr lnr-chevron-left"></i>
          </a>
          <div id="accounting" class="collapse ">
            <ul class="nav">
              <li><a href="{{ route('accounting.rollreceive.index') }}" ><span>Penerimaan Roll</span></a></li>
              <li><a href="{{ route('accounting.rollusage.index') }}" ><span>Pemakaian Roll</span></a></li>
            </ul>
          </div>
        </li>
        @endpermission
        <li><a href="panels.html" class=""><i class="lnr lnr-cog"></i> <span>Panels</span></a></li>
        <li><a href="notifications.html" class=""><i class="lnr lnr-alarm"></i> <span>Notifications</span></a></li>
        <li>
          <a href="#subPages" data-toggle="collapse" class="collapsed"><i class="lnr lnr-file-empty"></i> <span>Pages</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
          <div id="subPages" class="collapse ">
            <ul class="nav">
              <li><a href="page-profile.html" class="">Profile</a></li>
              <li><a href="page-login.html" class="">Login</a></li>
              <li><a href="page-lockscreen.html" class="">Lockscreen</a></li>
            </ul>
          </div>
        </li>
        <li><a href="tables.html" class=""><i class="lnr lnr-dice"></i> <span>Tables</span></a></li>
        <li><a href="typography.html" class=""><i class="lnr lnr-text-format"></i> <span>Typography</span></a></li>
        <li><a href="icons.html" class=""><i class="lnr lnr-linearicons"></i> <span>Icons</span></a></li>
      </ul>
    </nav>
  </div>
</div>
