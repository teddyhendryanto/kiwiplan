@extends('layouts.main')

@section('title', 'Summary Stock Roll')

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
      <li><a href="#">Accounting</a></li>
      <li><a href="#">Reports</a></li>
      <li><a href="{{ route('accounting.stocksummary.index') }}">Summary Stock Roll</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Summary Stock Roll</h3>
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
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control js-datatimepicker" name="date" value="" placeholder="Tanggal Stock" required>
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

          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="table-dataTable" class="table table-hover table-striped" width="100%">
                  <thead></thead>
      	          <tbody class="tbody searchable f13"></tbody>
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

    function reload_datatable(){
      var _date = $('[name="date"]').val();

      var _fileName = _date.replace(/-/g, '');

      var exportFormat = {
        body: function ( data, row, column, node ) {
            // Strip column to make it numeric
            return (column >= 2 && column <= 5) ? data.replace(/[.]/g, '').replace(',', '.') : data;
        },
      };

      $('#table-dataTable').DataTable({
        destroy: true,
        processing: true,
        searchable: false,
        ajax: {
          'url' : '{!! route('accounting.stocksummary.submit') !!}',
          'type': 'POST',
          'data': {
            _token : '{{ csrf_token() }}',
            date : _date,
          },
          'dataSrc': 'data'
        },
        columns: [
          { title: '#', data: 'paper_type', name: 'paper_type'},
          { title: 'Paper Group', data: 'paper_type_desc', name: 'paper_type_desc' },
          { title: 'Weight', data: 'weight', name: 'weight', render: $.fn.dataTable.render.number( '.', ',', 0 ) },
          { title: 'Cost', data: 'cost_wgt_local', name: 'cost_wgt_local', render: $.fn.dataTable.render.number( '.', ',', 2 )},
          { title: 'Value', data: 'value_wgt_local', name: 'value_wgt_local', render: $.fn.dataTable.render.number( '.', ',', 2 )},
          { title: '%', data: '%value_wgt_local', name: '%value_wgt_local', render: $.fn.dataTable.render.number( '.', ',', 2 )},
        ],
        columnDefs: [
          { className: "text-center w12-5", "targets": [ 0 ] },
          { className: "text-center w17-5", "targets": [ 1 ] },
          { className: "text-center w17-5", "targets": [ 2 ] },
          { className: "text-center w17-5", "targets": [ 3 ] },
          { className: "text-center w17-5", "targets": [ 4 ] },
          { className: "text-center w17-5", "targets": [ 5 ] },
        ],
        dom: 'lBfrtip',
        buttons: [
          // EXCEL BUTTON
          {
            extend : 'excelHtml5',
            text : 'Send to Excel',
            title: 'summary_rollstocks_'+_fileName+'_'+getCurrentDateTime(),
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
      });
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
