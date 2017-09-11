@extends('layouts.main')

@section('title', 'Paper Supplier')

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
      <li><a href="{{ route('suppliers.index') }}" class="active">Paper Supplier</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">List Supplier</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <a href="{{ route('suppliers.create') }}" class="btn btn-success">
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
      scrollX: true,
      scrollY: "80vh",
      scrollCollapse: true,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      pageLength: 100,
      ajax: {
        'url' : '{!! route('suppliers.ajax.getSupplierListDatatable') !!}',
        'type': 'POST',
        'data': {_token : '{{ csrf_token() }}'},
      },
      columns: [
          { data: 'id', name: 'id'},
          { data: 'code', name: 'code'},
          { data: 'full_name', name: 'full_name' },
          { data: 'address', name: 'address' },
          { data: 'lead_time', name: 'lead_time' },
          { data: 'currency', name: 'currency' },
          { data: 'term', name: 'term' },
          { data: 'action', name: 'action', orderable: false, searchable: false}
      ],
      columnDefs: [
        { title: "ID", className: "text-center w5", "targets": [ 0 ] },
        { title: "Short <br/> Name", className: "text-center w5", "targets": [ 1 ] },
        { title: "Full <br/> Name", className: "text-center w20", "targets": [ 2 ] },
        { title: "Address", className: "text-center w35", "targets": [ 3 ] },
        { title: "Lead <br/> Time", className: "text-center w5", "targets": [ 4 ] },
        { title: "Curr", className: "text-center w10", "targets": [ 5 ] },
        { title: "Term", className: "text-center w5", "targets": [ 6 ] },
        { title: "Action", className: "text-center w15", "targets": [ 7 ] },
      ],
      dom: 'lBfrtip',
      buttons: [
        // EXCEL BUTTON
        {
          extend : 'excelHtml5',
          text : 'Send to Excel',
          title: 'suppliers_'+getCurrentDateTime(),
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

  function ajax_suppliers_delete(_id){
    if (confirm('Yakin data mau dihapus?')) {
      $.ajax({
        type: "DELETE",
        url : 'suppliers/'+_id+'',
        dataType: "JSON",
        data : {"_token": "{{ csrf_token() }}"},
        success: function(data)
        {
          alert('Hapus permission berhasil.');
          _reload();
        }
      });
    }
	}

  $(document).ready(function(){
    reload_datatable();
  });
</script>
@endsection
