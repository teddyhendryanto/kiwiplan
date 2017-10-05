@extends('layouts.main')

@section('title', 'Penerimaan Roll Baru')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Bootstrap Datetimepicker -->
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="{{ route('receiveroll.index') }}">Penerimaan Roll</a></li>
      <li><a href="{{ route('receiveroll.create') }}" class="active">Buat Baru</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('receiveroll.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('receiveroll.store') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-6">
                    <!-- Left Side -->
                    <div class="row">
                      <div class="col-md-6">
                        <label for="type">Tipe <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="type">
                            <option value="AUTO">AUTOGENERATE</option>
                            <option value="FOX">FOX</option>
                            <option value="BOOKED">BOOKED</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="site">Site <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="site">
                            @foreach ($sites as $site)
                              <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="receive_date">Tgl Terima <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control js-datatimepicker" name="receive_date" value="{{ old('receive_date') }}" required>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_num">P.O# <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="po_id" value="">
                          <input type="text" name="po_num" class="form-control" value="{{ old('po_num') }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="supplier">Supplier <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="supplier_id" value="" required>
                          <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="paper_key">Kualitas Kertas</label>
                        <select class="form-control" name="paper_key" required>
                          <option value=""></option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="paper_width">Lebar Roll</label>
                        <select class="form-control" name="paper_width" required>
                          <option value=""></option>
                          @foreach ($widths as $width)
                            <option value="{{$width->width}}">{{$width->width}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="supplier_roll_id">Supplier Roll ID</label>
                        <div class="form-group">
                          <input type="text" name="supplier_roll_id" class="form-control" value="{{ old('supplier_roll_id') }}">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- RIght Side -->
                    <div class="row">
                      <div class="col-md-12">
                        <label for="unique_roll_id">Roll ID <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control" name="unique_roll_id" value="{{ old('unique_roll_id') }}" readonly>
                            <span class="input-group-btn">
                              <button class="btn btn-warning" type="button" name="generate-roll-id" disabled>Generate</button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="roll_weight">Berat Roll <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" name="roll_weight" class="form-control" value="{{ old('roll_weight') }}" required placeholder="KG" min="0" step="any">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="roll_diameter">Diameter Roll <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" name="roll_diameter" class="form-control" value="{{ old('roll_diameter') }}" required placeholder="MM" min="0" step="any">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="do_num">DO# <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" name="do_num" class="form-control" value="{{ old('do_num') }}" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="no_pol">Nomor Polisi <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" name="no_pol" class="form-control" value="{{ old('no_pol') }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <div class="form-group">
                          <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group text-right">
                      <input type="submit" name="submit" class="btn btn-default" value="Submit">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('pluginsjs')
  <!-- Parsley -->
	<script src="{{ asset('js/parsley.min.js') }}"></script>
  <!-- Moment JS -->
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- Bootstrap Datetimepicker -->
	<script src="{{ asset('vendor/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
@endsection

@section('script')
  <script type="text/javascript">
    $('document').ready(function(){
      $('.js-datatimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        keepOpen: false
      });

      // initialize parsley;
      $('#form').parsley({
        errorsContainer: function(el) {
          return el.$element.closest('.form-group');
        },
      });

      $('[name="type"]').change(function(){
        var _type = $(this).val();

        if(_type == "FOX"){
          $('[name="generate-roll-id"]').removeAttr('disabled');
          $('[name="unique_roll_id"]').removeAttr('readonly');
          $('[name="unique_roll_id"]').attr('required',true);
        }
      });

      $('[name="po_num"]').change(function(){
  			var _siteId = $('[name="site"]').val();
  			var _poNum = $(this).val();

  			if(_poNum.substr(_poNum.length - 1)=="X"){
  				$('[name="remarks"]').val('X');
  				$('[name="remarks"]').attr('readonly','readonly');
  				$('[name="supplier_roll_id"]').attr('readonly','readonly');
  			}
  			else{
  				$('[name="remarks"]').val('');
  				$('[name="remarks"]').removeAttr('readonly');
  				$('[name="supplier_roll_id"]').removeAttr('readonly');
  			}

  			$.ajax({
  				type: "POST",
  				url: "{{ route('receiveroll.ajax.getPODetail') }}",
  				data: { _token : '{{ csrf_token() }}', site_id : _siteId, po_num : _poNum},
  				datatype:"JSON",
  				success: function(data){
  					if(data.status == true){
              var _dataset = data.dataset;
  						var _suppId = _dataset.supplier_id;
  						var _suppName = _dataset.supplier.short_name;
  						var _poId = _dataset.id;
              var _paperKey = _dataset.supplier.keys;

              $('[name="supplier_id"]').val(_suppId);
  						$('[name="supplier"]').val(_suppName);
  						$('[name="po_id"]').val(_poId);

              var _str = "";
              $.each( _paperKey, function( key, value ) {
                var _paperKey = value.paper_key;
    						_str = _str + '<option value="'+_paperKey+'">'+_paperKey+'</option>';
              });
    					$('[name="paper_key"]').html(_str);
  					}
  					else{
  						alert('PO Tidak Ditemukan');
  						$('[name="po_num"]').focus();
  					}
  				}
  			});
  		});

      $('[name="generate-roll-id"]').click(function(){
        var _rtype = $('[name="type"]').val();
  			var _paperKey = $('[name="paper_key"]').val();
  			var _paperWidth = $('[name="paper_width"]').val();
  			var _supplierId = $('[name="supplier_id"]').val();

  			if(_paperKey!="" && _paperWidth!=""){
  				console.log(_paperKey,_paperWidth);

  				$.ajax({
  					type: "POST",
  					url: "{{ route('receiveroll.ajax.getFoxRollID')  }}",
  					data: {
              _token : '{{ csrf_token() }}',
              paper_key : _paperKey,
              paper_width:_paperWidth,
              supplier_id : _supplierId,
              rtype : _rtype
            },
  					datatype:"JSON",
  					success: function(data){
  						console.log(data);
  						if(data.status == true){
  							$('[name="unique_roll_id"]').val(data.rollid);
  						}
  						else{
  							alert('Error Saat Generate Roll ID');
  						}
  					}
  				});
  			}
  			else{
  				alert('Kualitas kertas dan lebar roll tidak boleh kosong.');
  			}
  		});

      $('form').submit(function(){
        var _rtype = $('[name="type"]').val();

        if(_rtype == 'FOX'){
          var _rollId = $('[name="unique_roll_id"]').val();
    			var _counter = _rollId.substr(_rollId.length - 5);

          if(_counter == 'XXXXX'){
    				alert('Roll ID Harus Diisi dengan Format yang Benar.');
    				$('[name="unique_roll_id"]').focus();
    				return false;
    			}
    			else if(_rollId.length != 15){
    				alert('Roll ID Lebih / Kurang dari 15 Karakter.');
    				$('[name="unique_roll_id"]').focus();
    				return false;
    			}
    			else{
    				return true;
    			}
        }
        else{
          return true;
        }

  		});

    });
  </script>
@endsection
