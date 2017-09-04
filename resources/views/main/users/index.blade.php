@extends('layouts.main')

@section('title', 'User')

@section('pluginscss')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('vendor/datatables/media/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">User</a></li>
      <li><a href="{{ route('users.index') }}" class="active">User</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <div class="col-md-12">
              <h3 class="panel-title">List User</h3>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <a href="{{ route('users.create') }}" class="btn btn-success">
            Tambah
          </a>
  				<div class="table-responsive mt10">
  	        <table id="table" class="table table-hover" width="100%">
  	          <thead>
  	            <tr>
                  <th>#</th>
                  <th>Username</th>
    							<th>Nama</th>
                  <th>Email</th>
                  <th>Role</th>
    							<th>Aksi</th>
  	            </tr>
  	          </thead>
  	          <tbody class="tbody searchable">
  	          </tbody>
  	        </table>
  		    </div>
        </div>
      </div>
      <div id="btn-navigation">
        <a href="{{ route('users.create') }}">
          <button type="button" class="btn btn-default" name="button">New Admin</button>
        </a>
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
      url : '{{ route('users.ajax.datatable') }}',
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
          var _username = _data[idx].username;
          var _name     = _data[idx].name;
          var _email    = _data[idx].email;
          var _role     = _data[idx].role_name;
          var _action 	= ""
          + "<a href='users/"+_id+"/edit' title='Edit'>"
          + "<button type='button' name='btn-edit' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-pencil'></i>"
          + "</button>"
          + "</a>"
          + "<a href='javascript:void(0)' onClick='ajax_users_delete("+_id+"); return false;' title='Delete'>"
          + "<button type='button' name='btn-delete' class='btn btn-default btn-xs' value='"+_id+"'>"
          + "<i class='fa fa-trash'></i>"
          + "</button>"
          + "</a>";
          _dataset.push([_j,_username,_name,_email,_role,_action]);
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
            { className: "text-center w7-5", "targets": [ 0 ] },
            { className: "text-center w12-5", "targets": [ 1 ] },
            { className: "text-center w25", "targets": [ 2 ] },
            { className: "text-center w25", "targets": [ 3 ] },
            { className: "text-center w15", "targets": [ 4 ] },
            { className: "text-center w15", "targets": [ 5 ] },
          ]
        });
      }
    });

    return reload_datatable; //return the function itself to reference

  }());

  function ajax_users_delete(_id){
    if (confirm('Yakin data mau dihapus?')) {
      $.ajax({
        type: "DELETE",
        url : 'users/'+_id+'',
        dataType: "JSON",
        data : {"_token": "{{ csrf_token() }}"},
        success: function(data)
        {
          alert('Hapus user berhasil.');
          _reload();
        }
      });
    }
	}

  $(document).ready(function(){

  });
</script>
@endsection
