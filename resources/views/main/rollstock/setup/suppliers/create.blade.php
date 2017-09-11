@extends('layouts.main')

@section('title', 'New Paper Supplier')

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
      <li><a href="{{ route('suppliers.index') }}" class="active">Paper Supplier</a></li>
      <li><a href="{{ route('suppliers.create') }}" class="active">Supplier Baru</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('suppliers.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('suppliers.store') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-2">
                    <label for="code">Kode Supplier <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="code" name="code" class="form-control text-uppercase" required value="{{ old('code') }}">
                    </div>
                  </div>
                  <div class="col-md-1">
                    <label for="ex_code">Ex Kode</label>
                    <div class="form-group">
                      <input type="text" id="ex_code" name="ex_code" class="form-control text-uppercase" value="{{ old('ex_code') }}">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="short_name">Supplier Short Name <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="short_name" name="short_name" class="form-control text-uppercase" required value="{{ old('short_name') }}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="full_name">Nama Full Supplier <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="full_name" name="full_name" class="form-control text-uppercase" required value="{{ old('full_name') }}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="address">Alamat</label>
                    <div class="form-group">
                      <input type="text" id="address" name="address" class="form-control text-uppercase" value="{{ old('address') }}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <label for="lead_time">Lead Time <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="lead_time" name="lead_time" class="form-control" value="{{ old('lead_time') }}" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label for="currency">Currency <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="currency" name="currency" class="form-control text-uppercase" value="{{ old('currency') }}" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label for="term">Term <span class="text-red">*</span></label>
                    <div class="form-group">
                      <input type="text" id="term" name="term" class="form-control" value="{{ old('term') }}" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <label for="contact_person">Contact Person</label>
                    <div class="form-group">
                      <input type="text" id="contact_person" name="contact_person" class="form-control text-uppercase" value="{{ old('contact_person') }}">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label for="name">Tepepon</label>
                    <div class="form-group">
                      <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label for="fax">Fax</label>
                    <div class="form-group">
                      <input type="text" id="fax" name="fax" class="form-control" value="{{ old('fax') }}">
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
@endsection

@section('script')
  <script type="text/javascript">
    $('document').ready(function(){
      // initialize parsley;
      $('#form').parsley();

    });
  </script>
@endsection
