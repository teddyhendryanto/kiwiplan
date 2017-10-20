@extends('layouts.main')

@section('title', 'New Purchase Order Frequent')

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
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('purchase_order_frequents.index') }}">Purhcase Order Frequent</a></li>
      <li><a href="{{ route('purchase_order_frequents.create') }}" class="active">Buat Baru</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('purchase_order_frequents.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('purchase_order_frequents.store') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-3">
                        <label for="paper_supplier">Suppier <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="paper_supplier" required autofocus>
                            <option value=""></option>
                            @foreach ($paper_suppliers as $supplier)
                              <option value="{{ $supplier->id }}">{{ $supplier->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="paper_quality">Kualitas Kertas <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="paper_quality" required autofocus>
                            <option value=""></option>
                            @foreach ($paper_qualities as $quality)
                              <option value="{{ $quality->quality }}">{{ $quality->quality }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <label for="paper_gramatures">Gramatur Kertas <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" name="paper_gramatures" class="form-control" value="{{ old('paper_gramatures') }}" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="paper_width">Lebar Kertas</label>
                        <div class="form-group">
                          <input type="text" name="paper_width" class="form-control" value="{{ old('paper_width') }}">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="submit">&nbsp;</label>
                        <div class="form-group">
                          <input type="submit" name="submit" class="btn btn-default" value="Submit">
                        </div>
                      </div>
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
