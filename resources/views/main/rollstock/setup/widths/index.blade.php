@extends('layouts.main')

@section('title', 'Paper Quality')

@section('pluginscss')
  <!-- Datatables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('widths.index') }}" class="active">Lebar Kertas</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">List Lebar Kertas</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <a href="{{ route('widths.create') }}" class="btn btn-success">
                Tambah
              </a>
            </div>
          </div>
  				<div class="table-responsive mt10">
  	        <table id="table-dataTable" class="table table-hover" width="100%">
  	          <thead></thead>
  	          <tbody class="tbody searchable f12">
  	          </tbody>
  	        </table>
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

  function reload_datatable(){

    $('#table-dataTable').DataTable({
      destroy: true,
      processing: true,
      serverSide: true,
      responsive: true,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      pageLength: 100,
      ajax: {
        'url' : '{!! route('widths.ajax.getWidthListDatatable') !!}',
        'type': 'POST',
        'data': {_token : '{{ csrf_token() }}'},
      },
      columns: [
        { data: 'rownum', name: 'rownum'},
        { data: 'width', name: 'width'},
        { data: 'action', name: 'action', orderable: false, searchable: false}
      ],
      columnDefs: [
        { title: "#", className: "text-center w10", "targets": [ 0 ] },
        { title: "Lebar", className: "text-center w45", "targets": [ 1 ] },
        { title: "Action", className: "text-center w45", "targets": [ 2 ] },
      ],
      dom: 'lBfrtip',
      buttons: [
        // EXCEL BUTTON
        {
          extend : 'excelHtml5',
          text : 'Send to Excel',
          title: 'widths_'+getCurrentDateTime(),
          footer: true,
          exportOptions: {
            modifier: {
                search: 'applied',
                order: 'applied'
            },
          }
        }
      ]
    });
  }

  $(document).ready(function(){
    reload_datatable();
  });
</script>
@endsection
