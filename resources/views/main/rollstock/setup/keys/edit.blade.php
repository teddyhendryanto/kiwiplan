@extends('layouts.main')

@section('title', 'Edit Paper Supplier')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('keys.index') }}" class="active">Paper Key</a></li>
      <li><a href="{{ route('keys.edit', Request::segment(4)) }}" class="active">Edit Paper Key</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('keys.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('keys.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-4">
                    <label for="code">Supplier <span class="text-red">*</span></label>
                    <div class="form-group">
                      <select class="form-control" name="supplier" required>
                        <option value=""></option>
                        @foreach ($suppliers as $supplier)
                          @if ($supplier->id == $data->id)
                            <option value="{{ $supplier->id }}" selected>{{ $supplier->short_name }}</option>
                          @else
                            <option value="{{ $supplier->id }}">{{ $supplier->short_name }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label for="code">Paper Key <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="paper_key" name="paper_key" class="form-control text-uppercase" required value="{{ $data->paper_key }}" autofocus>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="submit" name="submit" class="btn btn-default" value="Update">
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
@endsection

@section('script')
  <script type="text/javascript">
    $('document').ready(function(){
      // initialize parsley;
      $('#form').parsley();

    });
  </script>
@endsection
