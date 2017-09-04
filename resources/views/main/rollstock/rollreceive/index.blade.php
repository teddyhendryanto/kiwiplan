@extends('layouts.main')

@section('title', 'Penerimaan Roll')

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
      <li><a href="#">Roll Stock</a></li>
      <li><a href="{{ route('rollstocks.rollreceive.index') }}">Penerimaan Roll</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Penerimaan Roll</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="#">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="value" value="{{ $value }}">
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
                      <input type="button" name="submit" class="btn btn-default" value="Submit">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="table-dataTable" class="table table-hover table-striped" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">Tgl Penerimaan</th>
                      <th class="text-center">Docket</th>
                      <th class="text-center">Order ID</th>
                      <th class="text-center">Kode Kertas</th>
                      <th class="text-center">Roll ID</th>
                      <th class="text-center">Weight</th>
                      <th class="text-center">Cost</th>
                    </tr>
                  </thead>
      	          <tbody class="tbody searchable f13"></tbody>
                  <tfoot>
                    <tr class="text-bold">
                      <th colspan="6" class="w80">Total</th>
                      <th class="w10"></th>
                      <th class="w10"></th>
                    </tr>
                  </tfoot>
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
  <script src="{{ asset('js/moment.js') }}"></script>
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
      var _type     = $('[name="type"]').val();
      var _value    = $('[name="value"]').val();

      var _fileName = _dateFrom.replace(/-/g, '')+"_"+_dateTo.replace(/-/g, '');

      var exportFormat = {
        body: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return (column == 6 || column == 7) ?
                data.replace( /[.]/g, '' ) :
                data;
        },
        footer: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return data.replace( /[.]/g, '' );
        },
      };

      // Remove the formatting to get integer data for summation
      var intVal = function ( i ) {
        return typeof i === 'string' ?
          i.replace(/[\$.]/g, '')*1 :
        typeof i === 'number' ?
          i : 0;
      };

      $('#table-dataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        scrollY: "80vh",
        scrollCollapse: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 100,
        ajax: {
          'url' : '{!! route('rollstocks.rollreceive.submit') !!}',
          'type': 'POST',
          'data': {
            _token : '{{ csrf_token() }}',
            date_from : _dateFrom,
            date_to   : _dateTo,
            type      : _type,
            value     : _value,
          },
        },
        columns: [
          { data: 'rownum', name: 'rownum'},
          { data: 'received_js', name: 'received_js' },
          { data: 'docket_number', name: 'docket_number' },
          { data: 'order_id', name: 'order_id' },
          { data: 'paper_code', name: 'paper_code' },
          { data: 'unique_roll_id', name: 'unique_roll_id' },
          { data: 'weight', name: 'weight', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
          { data: 'cost_wgt_local', name: 'cost_wgt_local', render: $.fn.dataTable.render.number( '.', ',', 0 )},
        ],
        columnDefs: [
          { className: "text-center w5", "targets": [ 0 ] },
          { className: "text-center w15", "targets": [ 1 ] },
          { className: "text-center w15", "targets": [ 2 ] },
          { className: "text-center w15", "targets": [ 3 ] },
          { className: "text-center w15", "targets": [ 4 ] },
          { className: "text-center w15", "targets": [ 5 ] },
          { className: "text-right w10", "targets": [ 6 ] },
          { className: "text-right w10", "targets": [ 7 ] },
        ],
        order: [[0, 'asc']],
        rowGroup: {
          startRender: null,
          endRender: function ( rows, group ) {
              var weight = rows
                  .data()
                  .pluck('weight')
                  .reduce( function (sum, value) {
                      return parseInt(sum) + parseInt(value);
                  }, 0);

              weight      = $.fn.dataTable.render.number('.', ',', 0).display( weight );

              return $('<tr class="text-bold"/>')
                  .append( '<td colspan="6" class="text-center">Subtotal '+group+'</td>' )
                  .append( '<td class="text-right">'+weight+'</td>' )
                  .append( '<td/>' );
          },
          dataSrc: 'docket_number'
        },
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'rollreceives_'+_fileName+'_'+getCurrentDateTime(),
            footer: true,
            exportOptions: {
              modifier: {
                  search: 'applied',
                  order: 'applied'
              },
              format: exportFormat,
            }
          }
        ],
        footerCallback: function ( row, data, start, end, display ) {
          var api = this.api(), data;
          var sumColumns = [6]

          sumColumns.forEach(function(colIndex){
            // Total over all pages
            var total = api
                .column(colIndex)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total over this page
            var pageTotal = api
                .column(colIndex, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column(colIndex).footer() ).html(
                number_format(total,0,',','.')
            );
          });
        },
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
