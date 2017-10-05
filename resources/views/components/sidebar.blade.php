<div id="sidebar-nav" class="sidebar">
  <div class="sidebar-scroll">
    <nav>
      <ul class="nav">
        <li><a href="{{ route('home') }}">
          <span>Dashboard</span></a>
        </li>
        @role('superuser')
        <li>
          <a href="#setup" data-toggle="collapse" class="collapsed">
            <span>Setup</span>
            <i class="icon-submenu fa fa-angle-right"></i>
          </a>
          <div id="setup" class="collapse ">
            <ul class="nav">
              <li><a href="{{ route('notifications.index') }}" ><span>Notifications</span></a></li>
            </ul>
          </div>
        </li>
        <li>
          <a href="#users" data-toggle="collapse" class="collapsed">
            <span>User</span>
            <i class="icon-submenu fa fa-angle-right"></i>
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
            <i class="icon-submenu fa fa-angle-right"></i>
          </a>
          <div id="accounting" class="collapse ">
            <ul class="nav">
              <li><a href="{{ route('purchase_orders.index') }}"><span>P.O Kertas</span></a></li>
              <li><a href="{{ route('exchange_rates.index') }}"><span>Kurs</span></a></li>
              <li>
                <a href="#accounting-reports" data-toggle="collapse" class="collapsed">
                  <span>Report Kiwi<span>
                  <i class="icon-submenu fa fa-angle-right"></i>
                </a>
                <div id="accounting-reports" class="collapse ">
                  <ul class="nav">
                    <li><a href="{{ route('accounting.rollreceivesummary.index') }}"><span>Sum Penerimaan Roll</span></a></li>
                    <li><a href="{{ route('accounting.rollusagesummary.index') }}" ><span>Sum Pemakaian Roll</span></a></li>
                    <li><a href="{{ route('accounting.stocksummary.index') }}"><span>Sum Stock Roll</span></a></li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </li>
        @endpermission
        @permission('rollstock-access')
        <li>
          <a href="#rollstock" data-toggle="collapse" class="collapsed">
            <span>Roll Stock</span>
            <i class="icon-submenu fa fa-angle-right"></i>
          </a>
          <div id="rollstock" class="collapse ">
            <ul class="nav">
              @role(array('superuser','rollstock-spv'))
              <li>
                <a href="#rollstock-setup" data-toggle="collapse" class="collapsed">
                  <span>Setup<span>
                  <i class="icon-submenu fa fa-angle-right"></i>
                </a>
                <div id="rollstock-setup" class="collapse ">
                  <ul class="nav">
                    <li><a href="{{ route('suppliers.index') }}"><span>Supplier Kertas</span></a></li>
                    <li><a href="{{ route('qualities.index') }}"><span>Kualitas Kertas</span></a></li>
                    <li><a href="{{ route('gramatures.index') }}"><span>Gramatur Kertas</span></a></li>
                    <li><a href="{{ route('widths.index') }}"><span>Lebar Kertas</span></a></li>
                    <li><a href="{{ route('keys.index') }}"><span>Paper Key</span></a></li>
                  </ul>
                </div>
              </li>
              @endrole
              <li>
                <a href="#rollstock-paperroll" data-toggle="collapse" class="collapsed">
                  <span>Paper Roll<span>
                  <i class="icon-submenu fa fa-angle-right"></i>
                </a>
                <div id="rollstock-paperroll" class="collapse ">
                  <ul class="nav">
                    <li><a href="{{ route('receiveroll.index') }}"><span>Penerimaan Roll</span></a></li>
                    @role(array('superuser','rollstock-spv'))
                    <li><a href="{{ route('verifyroll.index') }}"><span>Verifikasi Roll</span></a></li>
                    <li><a href="{{ route('edi.index') }}"><span>Export EDI</span></a></li>
                    @endrole
                  </ul>
                </div>
              </li>
              <li>
                <a href="#rollstock-reports" data-toggle="collapse" class="collapsed">
                  <span>Report Kiwi<span>
                  <i class="icon-submenu fa fa-angle-right"></i>
                </a>
                <div id="rollstock-reports" class="collapse ">
                  <ul class="nav">
                    <li><a href="{{ route('rollstocks.rollreceive.index') }}" ><span>Penerimaan Roll</span></a></li>
                    <li><a href="{{ route('rollstocks.rollusage.index') }}" ><span>Pemakaian Roll</span></a></li>
                    <li><a href="{{ route('rollstocks.stock.index') }}" ><span>Stock Balance</span></a></li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </li>
        @endpermission
        <li><a href="{{ route('notif.index','unread') }}">
          <span>Notifikasi</span></a>
        </li>
        <!--
        <li><a href="notifications.html" class=""><i class="lnr lnr-alarm"></i> <span>Notifications</span></a></li>
        <li>
          <a href="#subPages" data-toggle="collapse" class="collapsed"><i class="lnr lnr-file-empty"></i> <span>Pages</span> <i class="icon-submenu fa fa-angle-right"></i></a>
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
        -->
      </ul>
    </nav>
  </div>
</div>
