<ul id="nav-status" class="nav nav-pills nav-stacked">
  <li class="">
    <a href="{{ route('notif.index', 'all') }}">
      <i class="fa fa-file-text-o"></i> All
    </a>
  </li>
  <li class="">
    <a href="{{ route('notif.index', 'unread') }}">
      <i class="fa fa-inbox"></i> Unread
      {{-- @if ($unread_notifs > 0)
        <span class="label label-primary pull-right">
          {{ $unread_notifs }}
        </span>
      @endif --}}
    </a>
  </li>
  <li class="">
    <a href="{{ route('notif.index', 'read') }}">
    <i class="fa fa-envelope-o"></i> Read</a>
  </li>
</ul>
