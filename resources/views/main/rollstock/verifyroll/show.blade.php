@extends('layouts.main')

@section('title', 'Verfikasi Roll Baru')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Date Range Picker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Paper Roll</a></li>
      <li><a href="{{ route('verifyroll.index') }}">Verifikasi Roll</a></li>
      <li><a href="{{ route('verifyroll.unverified') }}" class="active">Unverified Roll</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('verifyroll.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          @if(isset($details))
            <form id="form-verify" class="form" role="form" method="POST" action="{{ route('verifyroll.unverified.store') }}">
              {{ csrf_field() }}
              @if(isset($data))
              <input type="hidden" name="_method" value="PUT">
              @endif
              <div class="row">
        				<div class="col-md-12">
        					<div class="page-header">
        					  <h3>Unverified Roll</h3>
        					</div>
        				</div>
        			</div>
              @if (count($details) > 0)
              <div class="row mb5">
                <div class="col-md-6">
                  <input type="submit" class="btn btn-default" name="btn-verify" value="Submit">
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
                        </tr>
                      </thead>
          	          <tbody class="tbody searchable f12">
                        @php
                        $i = 1;
                        $grand_total_weight = 0;
                        $subtotal_weight = 0;
                        @endphp
                        @foreach ($details as $key => $data)
                          <tr>
                            <td class="text-center w5">
                              <input type="checkbox" name="cb[]" value="{{ $data->id }}">
                            </td>
                            <td class="text-center w2-5">{{ $i }}.</td>
                            <td class="text-center w7-5">{{ date('Y-m-d', strtotime($data->receive_date)) }} <br/> {{ $data->receive_time }} </td>
                            <td class="text-center w10">{{ $data->po_num }}</td>
                            <td class="text-center w10">{{ $data->supplier->short_name }}</td>
                            <td class="text-center w7-5">{{ $data->paper_key }}</td>
                            <td class="text-center w7-5">{{ $data->paper_width }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_weight,2,'.',',') }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_diameter,2,'.',',') }}</td>
                            <td class="text-center w12-5">{{ $data->unique_roll_id }}</td>
                            <td class="text-center w12-5">{{ $data->supplier_roll_id }}</td>
                            <td class="text-center w10">{{ $data->doc_ref }} <br/> {{ $data->wagon }}</td>
                          </tr>
                          @php
                          $i++;
                          $subtotal_weight += $data->roll_weight;
                          $grand_total_weight += $data->roll_weight;
                          @endphp
                          @if (@$details[$key+1]['doc_ref'] != $data['doc_ref'])
                            <tr class="subtotal">
                              <td colspan="7">Subtotal</td>
                              <td class="text-right">{{ number_format($subtotal_weight,2,'.',',') }}</td>
                              <td colspan="5"></td>
                            </tr>
                            @php
                              $subtotal_weight = 0;
                            @endphp
                          @endif
                        @endforeach
          	          </tbody>
                      <tfoot>
                        <tr class="grandtotal">
                          <td colspan="7">Grandtotal</td>
                          <td class="text-right">{{ number_format($grand_total_weight,2,'.',',') }}</td>
                          <td colspan="5"></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              @else
                <h4>Data tidak ditemukan.</h4>
              @endif
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
  <!-- Parsley -->
	<script src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Moment JS -->
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- Date Range Picker -->
  <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('script')
  <script type="text/javascript">
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

      $('#form-verify').submit(function(){
        if($('table').find('input[type=checkbox]:checked').length == 0){
  	        alert('Daftar history harus dipilih paling tidak 1.');
  					return false;
  	    }
  			else{
  				return true;
  			}
      });

    });
  </script>
@endsection
