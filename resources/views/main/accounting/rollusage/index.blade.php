@extends('layouts.main')

@section('title', 'Pemakaian Roll')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Date Range Picker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
  <!-- Datatables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/extensions/buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Accounting</a></li>
      <li><a href="{{ route('accounting.rollusage.index') }}">Pemakaian Roll</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Pemakaian Roll</h3>
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
                      <input type="button" name="submit" class="btn btn-default" value="Submit">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div id="table-result" class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="table-dataTable" class="table table-hover table-striped" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">Roll ID</th>
                      <th class="text-center">Tgl Pemakaian</th>
                      <th class="text-center">Paper Code</th>
                      <th class="text-center">Before</th>
                      <th class="text-center">Use</th>
                      <th class="text-center">Balance</th>
                    </tr>
                  </thead>
      	          <tbody class="tbody searchable f13"></tbody>
                  <tfoot>
                    <tr class="text-bold">
                      <th colspan="4" class="w55">Total</th>
                      <th class="w15"></th>
                      <th class="w15"></th>
                      <th class="w15"></th>
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
  <script type="text/javascript" src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Moment JS -->
  <script type="text/javascript" src="{{ asset('js/moment.js') }}"></script>
  <!-- Date Range Picker -->
  <script type="text/javascript" src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/buttons/js/buttons.flash.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/jszip.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/pdfmake.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/vfs_fonts.js')}}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.rowGroup.min.js')}}"></script>
  <script src="{{ asset('vendor/datatables/extensions/buttons/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/extensions/buttons/js/buttons.print.min.js') }}"></script>

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

    function reload_datatable(){
      var _dateFrom = $('[name="date_from"]').val();
      var _dateTo = $('[name="date_to"]').val();

      var _fileName = _dateFrom.replace(/-/g, '')+"_"+_dateTo.replace(/-/g, '');

      var exportFormat = {
        body: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return (column == 4 || column == 5 || column == 6) ?
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
        scrollY: "50vh",
    		scrollCollapse: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        ajax: {
          'url' : '{!! route('accounting.rollusage.submit') !!}',
          'type': 'POST',
          'data': {_token : '{{ csrf_token() }}', date_from : _dateFrom, date_to : _dateTo},
        },
        columns: [
            { data: 'rownum', name: 'rownum'},
            { data: 'unique_roll_id', name: 'unique_roll_id' },
            { data: 'finish_splice_js', name: 'finish_splice_js' },
            { data: 'paper_code', name: 'paper_code' },
            { data: 'weight_before_use', name: 'weight_before_use', render: $.fn.dataTable.render.number('.', ',', 0) },
            { data: 'weight_use', name: 'weight_use', render: $.fn.dataTable.render.number('.', ',', 0) },
            { data: 'weight_balance', name: 'weight_balance', render: $.fn.dataTable.render.number('.', ',', 0) },
        ],
        columnDefs: [
          { className: "text-center w10", "targets": [ 0 ] },
          { className: "text-center w15", "targets": [ 1 ] },
          { className: "text-center w15", "targets": [ 2 ] },
          { className: "text-center w15", "targets": [ 3 ] },
          { className: "text-right w15", "targets": [ 4 ] },
          { className: "text-right w15", "targets": [ 5 ] },
          { className: "text-right w15", "targets": [ 6 ] },
        ],
        order: [[3, 'asc']],
        rowGroup: {
          startRender: null,
          endRender: function ( rows, group ) {
              var before = rows
                  .data()
                  .pluck('weight_before_use')
                  .reduce( function (sum, value) {
                      return parseInt(sum) + parseInt(value);
                  }, 0);

              var use = rows
                  .data()
                  .pluck('weight_use')
                  .reduce( function (sum, value) {
                      return parseInt(sum) + parseInt(value);
                  }, 0);

              var balance = rows
                  .data()
                  .pluck('weight_balance')
                  .reduce( function (sum, value) {
                      return parseInt(sum) + parseInt(value);
                  }, 0);

              before      = $.fn.dataTable.render.number('.', ',', 0).display( before );
              use         = $.fn.dataTable.render.number('.', ',', 0).display( use );
              balance = $.fn.dataTable.render.number('.', ',', 0).display( balance );

              return $('<tr class="text-bold"/>')
                  .append( '<td colspan="4" class="text-center">Subtotal '+group+'</td>' )
                  .append( '<td class="text-right">'+before+'</td>' )
                  .append( '<td class="text-right">'+use+'</td>' )
                  .append( '<td class="text-right">'+balance+'</td>' );
          },
          dataSrc: 'paper_code'
        },
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'rollusages_'+_fileName+'#'+getCurrentDateTime(),
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
					var sumColumns = [4,5,6]

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
