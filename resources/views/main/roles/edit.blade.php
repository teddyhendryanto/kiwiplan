@extends('layouts.main')

@section('title', 'Edit Role')

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
      <li><a href="{{ route('roles.index') }}">Role</a></li>
      <li><a href="{{ route('roles.edit', Request::segment(3)) }}" class="active">Edit Role</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('roles.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('roles.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-3">
                    <label for="name">Nama Role <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="name" name="name" class="form-control text-transform-none"
                      value="{{ $data->name }}" autocomplete="off" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <label for="name">Display <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="display_name" name="display_name" class="form-control text-transform-none"
                      value="{{ $data->display_name }}" autocomplete="off" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <label for="name">Deskripsi</label>
                    <div class="form-group">
                      <input type="text" id="description" name="description" class="form-control text-transform-none"
                      value="{{ $data->description }}" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="permission">Permission</label>
                    <div class="form-group">
                      <select class="form-control select2-multi" id="permission" name="permission[]" multiple="multiple" required>
                        @foreach ($permissions as $permission)
                          <option value="{{$permission->id}}">{{$permission->display_name}}</option>
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
  <hr>
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
        {!! json_encode($data->perms()->allRelatedIds()) !!}
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
