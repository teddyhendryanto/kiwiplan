@extends('layouts.main')

@section('title', 'Permissions')

@section('pluginscss')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Admin</a></li>
      <li><a href="{{ route('permissions.index') }}" class="active">Permission</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">List Permission</h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <a href="{{ route('permissions.create') }}" class="btn btn-success">
                Tambah
              </a>
            </div>
          </div>
  				<div class="table-responsive mt10">
  	        <table id="table" class="table table-hover" width="100%">
  	          <thead>
  	            <tr>
                  <th>#</th>
                  <th>Nama Permission</th>
                  <th>Display</th>
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
  <!-- DataTables -->
	<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
@endsection

@section('script')
<script>

  var _reload = (function reload_datatable(){

    $.ajax({
      type: "POST",
      url : '{{ route('permissions.ajax.datatable') }}',
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
          var _name     = _data[idx].name;
          var _display  = _data[idx].display_name;
          var _desc     = _data[idx].description;
          var _action 	= ""
          + "<a href='permissions/"+_id+"/edit' title='Edit'>"
          + "<button type='button' name='btn-edit' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-pencil'></i>"
          + "</button>"
          + "</a>"
          + "<a href='javascript:void(0)' onClick='ajax_permissions_delete("+_id+"); return false;' title='Delete'>"
          + "<button type='button' name='btn-delete' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-trash'></i>"
          + "</button>"
          + "</a>";
          _dataset.push([_j,_name,_display,_desc,_action]);
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
            { className: "text-center w25", "targets": [ 1 ] },
            { className: "text-center w25", "targets": [ 2 ] },
            { className: "text-center w25", "targets": [ 3 ] },
            { className: "text-center w15", "targets": [ 4 ] },
          ]
        });
      }
    });

    return reload_datatable; //return the function itself to reference

  }());

  function ajax_permissions_delete(_id){
    if (confirm('Yakin data mau dihapus?')) {
      $.ajax({
        type: "DELETE",
        url : 'permissions/'+_id+'',
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

  });
</script>
@endsection
