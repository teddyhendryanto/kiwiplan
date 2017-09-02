@extends('layouts.main')

@section('title', 'Edit Admin')

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
      <li><a href="#">User</a></li>
      <li><a href="{{ route('users.index') }}">User</a></li>
      <li><a href="{{ route('users.edit', Request::segment(3)) }}" class="active">Edit User</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('users.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('users.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-6">
                    <label for="username">Username <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="username" name="username" class="form-control" required
                      value="{{ $data->username }}" disabled>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="name">Nama Lengkap <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="name" name="name" class="form-control" required
                      value="{{ $data->name }}" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="email">Email <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="email" id="email" name="email" class="form-control"
                      value="{{ $data->email }}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="password">Password <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="password" id="password" name="password" class="form-control"
                      value="{{ $data->password }}" autocomplete="off" disabled>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="role">Role <span class="text-red">*</span></label>
                    <div class="form-group">
                      <select class="form-control select2-multi" id="role" name="role[]" multiple="multiple" required>
                        @foreach ($roles as $role)
                          <option value="{{$role->id}}">{{$role->display_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
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
      // initialize parsley;
      $('#form').parsley({
        errorsContainer: function(el) {
            return el.$element.closest('.form-group');
        },
      });
      // initialize select2 multiple selection
      $('.select2-multi').select2();
      $(".select2-multi").select2().val(
        {!! json_encode($data->roles()->allRelatedIds()) !!}
      ).trigger('change');

    });
  </script>
@endsection
