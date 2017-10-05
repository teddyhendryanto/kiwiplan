@extends('layouts.main')

@section('title', 'Edit Kurs')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Bootstrap Datetimepicker -->
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Accounting</a></li>
      <li><a href="{{ route('exchange_rates.index') }}">Kurs</a></li>
      <li><a href="{{ route('exchange_rates.edit', Request::segment(3)) }}" class="active">Edit</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('exchange_rates.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('exchange_rates.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-3">
                        <label for="currency">Currency <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="currency" disabled autofocus>
                            <option value=""></option>
                            <option value="USD" selected>USD</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="rate_date">Date <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control js-datatimepicker" name="rate_date" value="{{ $data->rate_date }}" placeholder="Tanggal Kurs" required>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <label for="selling_rate">Kurs Jual</label>
                        <div class="form-group">
                          <input type="number" id="selling_rate" name="selling_rate" class="form-control" value="{{ $data->selling_rate }}" min="0" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="buying_rate">Kurs Beli</label>
                        <div class="form-group">
                          <input type="number" id="buying_rate" name="buying_rate" class="form-control" value="{{ $data->buying_rate }}" min="0" required>
                        </div>
                      </div>
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
  <!-- Moment JS -->
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- Bootstrap Datetimepicker -->
	<script src="{{ asset('vendor/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
@endsection

@section('script')
  <script type="text/javascript">

    $('document').ready(function(){
      $('.js-datatimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        keepOpen: false
      });

      // initialize parsley;
      $('#form').parsley();

    });
  </script>
@endsection
