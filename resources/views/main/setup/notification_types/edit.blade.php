@extends('layouts.main')

@section('title', 'Edit Permission')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('notifications.index') }}">Notifikasi</a></li>
      <li><a href="{{ route('notifications.edit', Request::segment(3)) }}" class="active">Edit Notifikasi</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('notifications.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('notifications.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-3">
                    <label for="name">Tipe Notifikasi <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="type" name="type" class="form-control text-lowercase" required
                      value="{{ $data->type }}" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="name">Deskripsi</label>
                    <div class="form-group">
                      <input type="text" id="description" name="description" class="form-control text-lowercase"
                      value="{{ $data->description }}" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="permission">User</label>
                    <div class="form-group">
                      <select class="form-control select2-multi" id="user" name="user[]" multiple="multiple">
                        @foreach ($users as $user)
                          <option value="{{$user->id}}">{{$user->username}} - {{$user->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="submit" name="submit" class="btn btn-default" value="Submit">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('pluginsjs')
  <!-- Parsley -->
  <script src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Select2 -->
  <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
@endsection

@section('script')
  <script type="text/javascript">
    $('document').ready(function(){
      // initialize select2 multiple selection
      $('.select2-multi').select2();
      $(".select2-multi").select2().val(
        {!! json_encode($data->users()->allRelatedIds()) !!}
      ).trigger('change');

      // initialize parsley;
      $('#form').parsley({
        errorsContainer: function(el) {
            return el.$element.closest('.form-group');
        },
      });

    });
  </script>
@endsection
