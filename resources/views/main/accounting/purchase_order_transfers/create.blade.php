@extends('layouts.main')

@section('title', 'Purchase Order')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Date Range Picker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
  <!-- Datatables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Accounting</a></li>
      <li><a href="{{ route('purchase_orders.index') }}">Purchase Order</a></li>
      <li><a href="{{ route('purchase_order_transfers.index') }}">Transfer</a></li>
      <li><a href="{{ route('purchase_order_transfers.create') }}">Buat Baru</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('purchase_order_transfers.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          @if(isset($purchase_orders) && count($purchase_orders) > 0)
            <form id="form-transfer" class="form" role="form" method="POST" action="{{ route('purchase_order_transfers.store') }}">
              {{ csrf_field() }}
              @if(isset($data))
              <input type="hidden" name="_method" value="PUT">
              @endif
              <div class="row">
        				<div class="col-md-12">
        					<div class="page-header">
        					  <h3>Purchase Order Transfer</h3>
        					</div>
        				</div>
        			</div>
              <div class="row mb5">
                <div class="col-md-6">
                  <input type="submit" class="btn btn-default" name="btn-verify" value="Bulk Transfer">
                </div>
                <div class="col-md-3 col-md-offset-3">
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
                          <th class="text-center w5">
                            <input type="checkbox" name="cb-all" value="">
                          </th>
                          <th class="text-center w5">#</th>
                          <th class="text-center w10">Site</th>
                          <th class="text-center w30">Supplier</th>
                          <th class="text-center w20">PO#</th>
                          <th class="text-center w20">PO Date</th>
                          <th class="text-center w15">Action</th>
                        </tr>
                      </thead>
          	          <tbody class="tbody searchable f12">
                        @php
                          $i = 1;
                        @endphp
                        @foreach ($purchase_orders as $data)
                          <tr>
                            <td class="text-center w5">
                              <input type="checkbox" name="cb[]" value="{{ $data->id }}">
                            </td>
                            <td class="text-center w5">{{ $i }}.</td>
                            <td class="text-center w10">{{ $data->site->short_name }} </td>
                            <td class="text-center w30">{{ $data->supplier->full_name }}</td>
                            <td class="text-center w20">{{ $data->po_num }}</td>
                            <td class="text-center w20">{{ date('Y-m-d', strtotime($data->po_date)) }}</td>
                            <td class="text-center w15">
                              <!-- <a href="#" class="btn btn-xs btn-default">
                                <i class="fa fa-sign-out"></i>
                              </a> -->
                              <a href="{{ route('purchase_orders.print', $data->id) }}" class="btn btn-xs btn-default" target="_blank">
                                <i class="fa fa-print"></i>
                              </a>
                            </td>
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
            </form>
          @else
            <h4>No data found.</h4>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection

@section('pluginsjs')
  <!-- Parsley JS -->
  <script src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Moment JS -->
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- Date Range Picker -->
  <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('script')
  <script>
    $(function() {
      var start = moment().startOf('month');
      var end = moment().endOf('month');

      function cb(start, end) {
        $('.daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('[name="date_from"]').val(start.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(end.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(end.diff(start, 'days')+1)
      }

      $('.daterange').daterangepicker({
          startDate: start,
          endDate: end,
          ranges: {
             'Hari Ini': [moment(), moment()],
             'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
             '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
             '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
             'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
             'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
      }, cb);

      cb(start, end);

    });

    $(document).ready(function(){
      $("th :checkbox").change(function() {
  		  if($(this).is(":checked")) {
  				$('td input:checkbox:not(:disabled)').prop('checked',true);
  	    }
  			else{
  				$('td input:checkbox').prop('checked',false);
  			}
  		});

      $('.daterange').on('hideCalendar.daterangepicker, apply.daterangepicker', function(ev, picker) {
        $('[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(picker.endDate.diff(picker.startDate, 'days')+1)
      });

      $('#form-transfer').submit(function(){
        if($('table').find('input[type=checkbox]:checked').length == 0){
          alert('Daftar purchase order harus dipilih paling tidak 1.');
					return false;
  	    }
  			else{
  				return true;
  			}
      });
    });
  </script>
@endsection
