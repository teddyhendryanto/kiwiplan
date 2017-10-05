@extends('layouts.main')

@section('title', 'Penerimaan Roll')

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
      <li><a href="{{ route('edi.index') }}">Export EDI</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Export EDI</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="{{ route('edi.showHistory') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-3">
                        <label for="daterange">Period</label>
                        <div class="form-group">
                          <div class="daterange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                          </div>
                          <input type="hidden" name="date_from" value="">
                          <input type="hidden" name="date_to" value="">
                          <input type="hidden" name="count_days" value="">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="submit">&nbsp;</label>
                        <div class="form-group">
                          <input type="submit" name="submit" class="btn btn-default" value="Search">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>

          </div>

          @if(isset($datas))
            <div class="row">
      				<div class="col-md-12">
      					<div class="page-header">
      					  <h3>History Export EDI</h3>
                  <h6><i>{{ $date_from }} s/d {{ $date_to }}</i></h6>
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
                        <th class="text-center w5">#</th>
                        <th class="text-center w15">Process Date </th>
                        <th class="text-center w15">Process By</th>
                        <th class="text-center w15">EDI File</th>
                        <th class="text-center w20">EDI Status</th>
                        <th class="text-center w10">Record Count</th>
                        <th class="text-center w5"></th>
                      </tr>
                    </thead>
        	          <tbody class="tbody searchable f12">
                      @if (count($datas) > 0)
                        @php
                          $i = 1;
                        @endphp
                        @foreach ($datas as $data)
                          <tr>
                            <td class="text-center w5">{{ $i }}.</td>
                            <td class="text-center w15">{{ $data->created_at }}</td>
                            <td class="text-center w15">{{ $data->created_by }}</td>
                            <td class="text-center w15">{{ $data->order_file }} <br/> {{ $data->receiving_file }}</td>
                            <td class="text-center w20">
                              @if (isset($data->edi_export_histories) && count($data->edi_export_histories) > 0)
                                @foreach ($data->edi_export_histories as $history)
                                  {{ $history->edi_status }} {{ $history->remarks }} <br/>
                                @endforeach
                              @endif
                            </td>
                            <td class="text-center w10">{{ $data->edi_export_details->count() }}</td>
                            <td class="text-center w5">
                              <a href="{{ route('edi.show', $data->id) }}" class="btn btn-default btn-xs" target="_blank">
                                <i class="fa fa-eye"></i>
                              </a>
                            </td>
                          </tr>
                          @php
                            $i++;
                          @endphp
                        @endforeach
                      @else
                        <tr>
                          <td colspan="7" class="text-center">No data</td>
                        </tr>
                      @endif

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
      var start = moment();
      var end = moment();

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
      $('.daterange').on('hideCalendar.daterangepicker, apply.daterangepicker', function(ev, picker) {
        $('[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(picker.endDate.diff(picker.startDate, 'days')+1)
      });

    });
  </script>
@endsection
