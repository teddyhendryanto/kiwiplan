@extends('layouts.main')

@section('title', 'Notifikasi')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="{{ route('notif.index', 'unread') }}">Notifikasi</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Notifikasi</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <div class="page-header">
                <h3>List Notifikasi</h3>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="row">
              <div class="col-md-3">
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title">Folders</h3>
                  </div>
                  <div class="box-body no-padding">
                    @include('components.notification-sidebar')
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <div class="box box-primary">
                  <div class="box-header with-border mb10">
                    <h3 class="box-title">{{ Request::segment(3) }} Notifications</h3>
                  </div>
                  <div class="box-body no-padding">
                    <div class="table-responsive mailbox-messages">
                      <table class="table table-hover table-striped">
                        <tbody>
                          @if (Request::segment(3) == 'unread')
                            @forelse (Auth::user()->unreadNotifications as $notification)
                              @php
                                if ($notification->read_by == null) {
                                  $class = 'text-bold';
                                } else {
                                  $class = '';
                                }
                              @endphp
                              <tr class="{{ $class }}">
                                <td>
                                  <button type="button" class="btn btn-xs" name="btn-read" onclick="markAsRead('{{$notification->id}}');">
                                    <i class="fa fa-check"></i>
                                  </button>
                                </td>
                                <td class="mailbox-name">
                                  {{ $notification->data['type_log'] }}
                                </td>
                                <td class="mailbox-message">
                                  {{ $notification->data['message'] }}
                                </td>
                                <td class="mailbox-date">
                                  {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </td>
                              </tr>
                            @empty
                              <tr>
                                <td colspan="5">No notification</td>
                              </tr>
                            @endforelse
                          @elseif (Request::segment(3) == 'all')
                            @foreach (Auth::user()->notifications as $notification)
                              @php
                                if ($notification->read_by == null) {
                                  $class = 'text-bold';
                                } else {
                                  $class = '';
                                }
                              @endphp
                              <tr class="{{ $class }}">
                                <td>
                                  <button type="button" class="btn btn-xs" name="btn-read" onclick="markAsRead('{{$notification->id}}');">
                                    <i class="fa fa-check"></i>
                                  </button>
                                </td>
                                <td class="mailbox-name">
                                  {{ $notification->data['type_log'] }}
                                </td>
                                <td class="mailbox-message">
                                  {{ $notification->data['message'] }}
                                </td>
                                <td class="mailbox-date">
                                  {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </td>
                              </tr>
                            @endforeach
                          @else
                            @foreach (Auth::user()->notifications as $notification)
                              @if($notification->read_by != "")
                                <tr class="">
                                  <td>
                                    <button type="button" class="btn btn-xs" name="btn-read" onclick="markAsRead('{{$notification->id}}');">
                                      <i class="fa fa-check"></i>
                                    </button>
                                  </td>
                                  <td class="mailbox-name">
                                    {{ $notification->data['type_log'] }}
                                  </td>
                                  <td class="mailbox-message">
                                    {{ $notification->data['message'] }}
                                  </td>
                                  <td class="mailbox-date">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                  </td>
                                </tr>
                              @endif
                            @endforeach
                          @endif

                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('pluginsjs')
  <!-- Parsley JS -->
  <script src="{{ asset('js/parsley.min.js') }}"></script>
@endsection

@section('script')
  <script>
  function markAsRead(notification_id){
    $.get('/markAsRead/'+notification_id, function(data, status, xhr){
  		console.log(data);
  		console.log(status);
  		console.log(xhr.status);
  		if(xhr.status == 200){
  			if(data.status == true) location.reload();
  			else alert('Error.');
  		}
  		else{
  			alert('Whoops. Error.');
  		}
    });
  }
  </script>
@endsection
