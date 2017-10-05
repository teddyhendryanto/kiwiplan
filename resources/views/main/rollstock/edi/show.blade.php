@extends('layouts.main')

@section('title', 'EDI Export')

@section('pluginscss')
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Paper Roll</a></li>
      <li><a href="{{ route('edi.index') }}">Export EDI</a></li>
      <li><a href="{{ route('edi.show', Request::segment(4)) }}">Show EDI {{ $data->edi_counter }}</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">EDI {{ $data->edi_counter }}</h3>
        </div>
        <div id="panel-body" class="panel-body">

          @if(isset($details))
            <div class="row">
      				<div class="col-md-12">
      					<div class="page-header">
      					  <h3>Daftar EDI {{ $data->edi_counter }}</h3>
      					</div>
      				</div>
      			</div>
            <div class="row mb5">
              <div class="col-md-3 col-md-offset-9">
                <div class="input-group"> <span class="input-group-addon">Filter</span>
                  <label class="sr-only" for="search-history">Search</label>
                  <input class="form-control" id="filter" name="search-history" placeholder="Type Here" type="text">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table id="table-history" class="table table-hover table-striped" width="100%">
                    <thead class="f12">
                      <tr>
                        <th class="text-center w2-5">#</th>
                        <th class="text-center w7-5">Tgl <br/> Receive</th>
                        <th class="text-center w10">PO#</th>
                        <th class="text-center w10">Supplier</th>
                        <th class="text-center w7-5">Paper <br/> Key</th>
                        <th class="text-center w7-5">Paper <br/> Width</th>
                        <th class="text-center w7-5">Weight <br/> (KG)</th>
                        <th class="text-center w7-5">Diam <br/> (MM)</th>
                        <th class="text-center w12-5">Unique <br/> Roll ID</th>
                        <th class="text-center w12-5">Supplier <br/>Roll ID</th>
                        <th class="text-center w10">Doc Ref <br/> Nopol</th>
                        <th class="text-center w15">Tgl <br/> Verifikasi</th>
                      </tr>
                    </thead>
        	          <tbody class="tbody searchable f12">
                      @php
                        $i = 1;
                      @endphp
                      @foreach ($details as $detail)
                        <tr>
                          <td class="text-center w2-5">{{ $i }}.</td>
                          <td class="text-center w7-5">{{ date('Y-m-d', strtotime($detail->verify_roll->receive_roll->receive_date)) }} <br/> {{ $detail->verify_roll->receive_roll->receive_time }} </td>
                          <td class="text-center w10">{{ $detail->verify_roll->receive_roll->po_num }}</td>
                          <td class="text-center w10">{{ $detail->verify_roll->receive_roll->supplier->short_name }}</td>
                          <td class="text-center w7-5">{{ $detail->verify_roll->receive_roll->paper_key }}</td>
                          <td class="text-center w7-5">{{ $detail->verify_roll->receive_roll->paper_width }}</td>
                          <td class="text-right w7-5">{{ number_format($detail->verify_roll->receive_roll->roll_weight,2,'.',',') }}</td>
                          <td class="text-right w7-5">{{ number_format($detail->verify_roll->receive_roll->roll_diameter,2,'.',',') }}</td>
                          <td class="text-center w12-5">{{ $detail->verify_roll->receive_roll->unique_roll_id }}</td>
                          <td class="text-center w12-5">{{ $detail->verify_roll->receive_roll->supplier_roll_id }}</td>
                          <td class="text-center w10">{{ $detail->verify_roll->receive_roll->doc_ref }} <br/> {{ $detail->verify_roll->receive_roll->wagon }}</td>
                          <td class="text-center w15">{{ date('Y-m-d', strtotime($detail->verify_roll->verify_date)) }}</td>
                        </tr>
                        @php
                          $i++;
                        @endphp
                      @endforeach
        	          </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection

@section('pluginsjs')
@endsection

@section('script')
  <script>
    $(document).ready(function(){


    });
  </script>
@endsection
