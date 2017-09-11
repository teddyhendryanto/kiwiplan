@extends('layouts.main')

@section('title', 'Notifikasi')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Setup</a></li>
      <li><a href="{{ route('notifications.index') }}" class="active">Notifikasi</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">List Notifikasi</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <a href="{{ route('notifications.create') }}" class="btn btn-success">
                Tambah
              </a>
            </div>
          </div>
  				<div class="table-responsive mt10">
  	        <table id="table" class="table table-hover" width="100%">
  	          <thead>
  	            <tr>
                  <th>#</th>
                  <th>Tipe Notifikasi</th>
                  <th>Deskripsi</th>
                  <th>Aksi</th>
  	            </tr>
  	          </thead>
  	          <tbody class="tbody searchable">
  	          </tbody>
  	        </table>
  		    </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('pluginsjs')
  <!-- Parsley -->
	<script src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Select2 -->
  <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap.min.js') }}"></script>
@endsection

@section('script')
<script>

  var _reload = (function reload_datatable(){

    $.ajax({
      type: "POST",
      url : '{{ route('notifications.ajax.datatable') }}',
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
          var _type     = _data[idx].type;
          var _desc     = _data[idx].description;
          var _action 	= ""
          + "<a href='notifications/"+_id+"/edit' title='Edit'>"
          + "<button type='button' name='btn-edit' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-pencil'></i>"
          + "</button>"
          + "</a>"
          + "<a href='javascript:void(0)' onClick='ajax_notifications_delete("+_id+"); return false;' title='Delete'>"
          + "<button type='button' name='btn-delete' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-trash'></i>"
          + "</button>"
          + "</a>";
          _dataset.push([_j,_type,_desc,_action]);
          _i = _i+1;
        });
        // console.log(_dataset);
        $("#table").DataTable({
          destroy: true,
          paging: true,
          searching: true,
          pageLength: 50,
          data : _dataset,
          columnDefs: [
            { className: "text-center w10", "targets": [ 0 ] },
            { className: "text-center w30", "targets": [ 1 ] },
            { className: "text-center w40", "targets": [ 2 ] },
            { className: "text-center w20", "targets": [ 3 ] },
          ]
        });
      }
    });

    return reload_datatable; //return the function itself to reference

  }());

  function ajax_notifications_delete(_id){
    if (confirm('Yakin data mau dihapus?')) {
      $.ajax({
        type: "DELETE",
        url : 'notifications/'+_id+'',
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
    // initialize select2 multiple selection
    $('.select2-multi').select2();

    // initialize parsley;
    $('#form').parsley({
      errorsContainer: function(el) {
          return el.$element.closest('.form-group');
      },
    });
  });
</script>
@endsection
