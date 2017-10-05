@extends('layouts.main')

@section('title', 'Stock Roll')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Bootstrap Datetimepicker -->
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}">
  <!-- Datatables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="{{ route('rollstocks.stock.index') }}">Stock</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">

      <div id="panel-info" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Stock Roll</h3>
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
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="input-group">
                        {{-- <span class="input-group-addon"><i class="fa fa-calendar"></i></span> --}}
                        <input type="text" class="form-control js-datatimepicker" name="date" value="" placeholder="Tanggal Stock" required>
                        <span class="input-group-btn">
                          <select name="report_by" class="btn btn-default">
                            <option value="detail" selected>Detail</option>
                            <option value="summary">Summary</option>
                          </select>
                        </span>
                      </div>
                    </div>
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

          <div id="result" class="row hide">
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
  <!-- Bootstrap Datetimepicker -->
	<script src="{{ asset('vendor/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
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
    function detail_report(_reportBy,_date){
      var _fileName = _date.replace(/-/g, '');

      var exportFormat = {
        body: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return (column >= 6 && column <= 7) ?
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
      $('#table-dataTable tfoot').append(""
        +'<tr class="text-bold">'
        +'<th colspan="4" class="w35">Total</th>'
        +'<th class="w10"></th>'
        +'<th class="w10"></th>'
        +'<th colspan="3" class="w45"></th>'
        +'</tr>'
      );
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
          'url' : '{!! route('rollstocks.stock.submit') !!}',
          'type': 'POST',
          'data': {_token : '{{ csrf_token() }}', report_by : _reportBy, date : _date},
        },
        columns: [
          { data: 'rownum', name: 'rownum'},
          { data: 'paper_code', name: 'paper_code' },
          { data: 'unique_roll_id', name: 'unique_roll_id' },
          { data: 'width', name: 'width' },
          { data: 'weight', name: 'weight', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
          { data: 'diameter', name: 'diameter', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
          { data: 'received_js', name: 'received_js' },
          { data: 'last_used_js', name: 'last_used_js' },
          { data: 'roll_aging', name: 'roll_aging', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
        ],
        columnDefs: [
          { "title": "#", className: "text-center w5", "targets": [ 0 ] },
          { "title": "Kode Kertas", className: "text-center w5", "targets": [ 1 ] },
          { "title": "Roll ID", className: "text-center w15", "targets": [ 2 ] },
          { "title": "Width", className: "text-center w10", "targets": [ 3 ] },
          { "title": "Weight", className: "text-right w10", "targets": [ 4 ] },
          { "title": "Diameter", className: "text-right w10", "targets": [ 5 ] },
          { "title": "Tgl Terima", className: "text-center w17-5", "targets": [ 6 ] },
          { "title": "Tgl Pakai", className: "text-center w17-5", "targets": [ 7 ] },
          { "title": "Aging", className: "text-center w10", "targets": [ 8 ] },
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

              var diameter = rows
                  .data()
                  .pluck('diameter')
                  .reduce( function (sum, value) {
                      return parseInt(sum) + parseInt(value);
                  }, 0);

              weight      = $.fn.dataTable.render.number('.', ',', 0).display( weight );
              diameter      = $.fn.dataTable.render.number('.', ',', 0).display( diameter );

              return $('<tr class="text-bold"/>')
                  .append( '<td colspan="4" class="text-center">Subtotal '+group+'</td>' )
                  .append( '<td class="text-right">'+weight+'</td>' )
                  .append( '<td class="text-right">'+diameter+'</td>' )
                  .append( '<td colspan="3"/>' );
          },
          dataSrc: 'paper_code'
        },
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'stocks_details_'+_fileName+'_'+getCurrentDateTime(),
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
          var sumColumns = [4,5]

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

    function summary_report(_reportBy,_date){

      var _fileName = _date.replace(/-/g, '');

      var exportFormat = {
        body: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return (column == 3 || column == 4) ?
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

      $('#table-dataTable tfoot').append(""
        +'<tr class="text-bold">'
        +'<th colspan="3" class="55">Total</th>'
        +'<th class="w22-5"></th>'
        +'<th class="w22-5"></th>'
        +'</tr>'
      );

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
          'url' : '{!! route('rollstocks.stock.submit') !!}',
          'type': 'POST',
          'data': {_token : '{{ csrf_token() }}', report_by : _reportBy, date : _date},
        },
        columns: [
          { data: 'rownum', name: 'rownum'},
          { data: 'paper_code', name: 'paper_code' },
          { data: 'width', name: 'width' },
          { data: 'sum_weight', name: 'sum_weight', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
          { data: 'count_roll', name: 'count_roll', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
        ],
        columnDefs: [
          { "title": "#", className: "text-center w10", "targets": [ 0 ] },
          { "title": "Kode Kertas", className: "text-center w22-5", "targets": [ 1 ] },
          { "title": "Width", className: "text-center w22-5", "targets": [ 2 ] },
          { "title": "Weight", className: "text-right w22-5", "targets": [ 3 ] },
          { "title": "Jumlah Roll", className: "text-right w22-5", "targets": [ 4 ] },
        ],
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'stocks_summary_'+_fileName+'_'+getCurrentDateTime(),
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
          var sumColumns = [3,4]

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

    function reload_datatable(){
      var _reportBy = $('[name="report_by"]').val();
      var _date = $('[name="date"]').val();

      if(_date == ""){
        $('[name="date"]').focus();
      }
      else{
        if(_reportBy == "detail"){
          detail_report(_reportBy,_date);
        }
        else{
          console.log('2');
          summary_report(_reportBy,_date);
        }

        $('#result').removeClass('hide');
      }

    }

    $(document).ready(function(){
      $('.js-datatimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        keepOpen: true
      });

      $('[name="submit"]').click(function(){
        reload_datatable();
      });
    });
  </script>
@endsection
