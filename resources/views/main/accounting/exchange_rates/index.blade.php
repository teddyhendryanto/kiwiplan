@extends('layouts.main')

@section('title', 'Kurs')

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
      <li><a href="{{ route('exchange_rates.index') }}">Kurs</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Kurs</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="#">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-3">
                    <div class="daterange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                      <i class="fa fa-calendar fa fa-calendar"></i>&nbsp;
                      <span></span> <b class="caret"></b>
                    </div>
                    <input type="hidden" name="date_from" value="">
                    <input type="hidden" name="date_to" value="">
                    <input type="hidden" name="count_days" value="">
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="button" class="btn btn-default" name="submit" value="Submit">
                    </div>
                  </div>
                  <div class="col-md-3 col-md-offset-3">
                    <a href="{{ route('exchange_rates.create') }}" class="btn btn-success pull-right">
                      Buat Baru
                    </a>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="row mt5">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="table-dataTable" class="table table-hover table-striped" width="100%">
                  <thead></thead>
      	          <tbody class="tbody searchable f13"></tbody>
                  <tfoot></tfoot>
                </table>
              </div>
            </div>
          </div>
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
  <!-- DataTables -->
  <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/Buttons/js/buttons.flash.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/jszip.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/pdfmake.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/vfs_fonts.js')}}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.rowGroup.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/extensions/Buttons/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/Buttons/js/buttons.print.min.js') }}"></script>
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

    function reload_datatable(){
      var _dateFrom = $('[name="date_from"]').val();
      var _dateTo   = $('[name="date_to"]').val();

      var _fileName = _dateFrom.replace(/-/g, '')+"_"+_dateTo.replace(/-/g, '');

      $('#table-dataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 100,
        ajax: {
          'url' : '{!! route('exchange_rates.ajax.getExchangeRateDatatable') !!}',
          'type': 'POST',
          'data': {
            _token : '{{ csrf_token() }}',
            date_from : _dateFrom,
            date_to   : _dateTo
          },
        },
        columns: [
          { data: 'rownum', name: 'rownum'},
          { data: 'currency', name: 'currency'},
          { data: 'rate_date', name: 'rate_date'},
          { data: 'selling_rate', name: 'selling_rate'},
          { data: 'buying_rate', name: 'buying_rate'},
          { data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        columnDefs: [
          { title: "#", className: "text-center w10", "targets": [ 0 ] },
          { title: "Currency", className: "text-center w18", "targets": [ 1 ] },
          { title: "Tgl Rate", className: "text-center w18", "targets": [ 2 ] },
          { title: "Rate Jual", className: "text-center w18", "targets": [ 3 ] },
          { title: "Rate Beli", className: "text-center w18", "targets": [ 4 ] },
          { title: "Action", className: "text-center w18", "targets": [ 5 ] },
        ],
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'exchange_rates_'+_fileName+'_'+getCurrentDateTime(),
            footer: true,
            exportOptions: {
              modifier: {
                  search: 'applied',
                  order: 'applied'
              }
            }
          }
        ],
      });
    }

    $(document).ready(function(){
      $('.daterange').on('hideCalendar.daterangepicker, apply.daterangepicker', function(ev, picker) {
        $('[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(picker.endDate.diff(picker.startDate, 'days')+1)
      });

      reload_datatable();

      $('[name="submit"]').click(function(){
        reload_datatable();
      });
    });
  </script>
@endsection
