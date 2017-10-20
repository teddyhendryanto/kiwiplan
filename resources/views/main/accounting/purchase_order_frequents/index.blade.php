@extends('layouts.main')

@section('title', 'Purchase Order Frequent')

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
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('purchase_order_frequents.index') }}">Purchase Order Frequent</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Purchase Order Frequent</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row mb5">
            <div class="col-md-12 text-right">
              <a href="{{ route('purchase_order_frequents.create') }}" class="btn btn-success">
                Buat Baru
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="table-dataTable" class="table table-hover table-striped" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
        							<th>Supplier</th>
                      <th>Paper Quality</th>
                      <th>Paper Gramatur</th>
                      <th>Paper Width</th>
        							<th>Aksi</th>
      	            </tr>
                  </thead>
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
  <!-- DataTables -->
  <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap.min.js') }}"></script>
@endsection

@section('script')
  <script>
    var _reload = (function reload_datatable(){

      $.ajax({
        type: "POST",
        url : '{{ route('purchase_order_frequents.ajax.getPurchaseOrderFrequentDatatable') }}',
        dataType: "JSON",
        data : {"_token": "{{ csrf_token() }}"},
        success: function(data)
        {
          var _dataset = [];
          var _data = data.dataset;
          var _i = 1;
          $(_data).each(function(idx,dataset){
            var _j 				= _i+'.';
            var _id    		= _data[idx].id;
            var _name     = _data[idx].supplier.short_name;
            var _quality  = _data[idx].paper_quality;
            var _gramatur = _data[idx].paper_gramatures;
            var _width    = _data[idx].paper_width;
            var _action 	= ""
            + "<a href='purchase_order_frequents/"+_id+"/edit' title='Edit'>"
            + "<button type='button' name='btn-edit' class='btn btn-default btn-xs' value='"+_id+"'>"
            + "<i class='fa fa-pencil'></i>"
            + "</button>"
            + "</a>"
            + "<a href='javascript:void(0)' onClick='ajax_purchase_order_frequents_delete("+_id+"); return false;' title='Delete'>"
            + "<button type='button' name='btn-delete' class='btn btn-default btn-xs' value='"+_id+"'>"
            + "<i class='fa fa-trash'></i>"
            + "</button>"
            + "</a>";
            _dataset.push([_j,_name,_quality,_gramatur,_width,_action]);
            _i = _i+1;
          });
          // console.log(_dataset);
          $("#table-dataTable").DataTable({
            destroy: true,
            paging: true,
            searching: true,
            pageLength: 50,
            data : _dataset,
            columnDefs: [
              { className: "text-center w10", "targets": [ 0 ] },
              { className: "text-center w20", "targets": [ 1 ] },
              { className: "text-center w20", "targets": [ 2 ] },
              { className: "text-center w20", "targets": [ 3 ] },
              { className: "text-center w20", "targets": [ 4 ] },
              { className: "text-center w10", "targets": [ 5 ] },
            ]
          });
        }
      });

      return reload_datatable; //return the function itself to reference

    }());

    function ajax_purchase_order_frequents_delete(_id){
      if (confirm('Yakin data ingin di hapus?')) {
        $.ajax({
          type: "DELETE",
          url : 'purchase_order_frequents/'+_id+'',
          dataType: "JSON",
          data : {"_token": "{{ csrf_token() }}"},
          success: function(data)
          {
            alert('Hapus data berhasil.');
            _reload();
          }
        });
      }
  	}

    $(document).ready(function(){
      // reload_datatable();
    });
  </script>
@endsection
