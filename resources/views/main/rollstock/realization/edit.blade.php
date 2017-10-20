@extends('layouts.main')

@section('title', 'Edit Realisasi P.O')

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
      <li><a href="#">Paper Roll</a></li>
      <li><a href="{{ route('purchase_order_realizations.index') }}">Realisasi P.O</a></li>
      <li><a href="{{ route('purchase_order_realizations.edit', Request::segment(4)) }}" class="active">Edit</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('purchase_order_realizations.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('purchase_order_realizations.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-6">
                    <!-- Left Side -->
                    <div class="row">
                      <div class="col-md-6">
                        <label for="site">Site <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="site">
                            @foreach ($sites as $site)
                              @if ($site->id == $data->purchase_order->site_id)
                                <option value="{{ $site->id }}" selected>{{ $site->short_name }}</option>
                              @else
                                <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_num">P.O# <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="po_id" value="{{ $data->purchase_order_id }}">
                          <input type="text" name="po_num" class="form-control" value="{{ $data->purchase_order->po_num }}" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <label for="supplier">Supplier</label>
                        <div class="form-group">
                          <input type="hidden" name="supplier_id" value="{{ $data->purchase_order->supplier_id }}" required>
                          <input type="text" name="supplier" class="form-control" value="{{ $data->purchase_order->supplier->short_name }}" readonly>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <label for="total_qty">Total P.O Qty</label>
                        <div class="form-group">
                          <input type="text" name="total_qty" class="form-control" value="{{ $data->paper_qty }}" readonly>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <label for="paper_quality">Kualitas Kertas P.O</label>
                        <input type="hidden" name="paper_q" value="{{ substr($data->paper_key,2,2) }}">
                        <select class="form-control" name="paper_quality" required>
                          <option value=""></option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- RIght Side -->
                    <div class="row">
                      <div class="col-md-4">
                        <label for="realization_key">Kualitas Kertas Realisasi</label>
                        <select class="form-control" name="realization_key" required>
                          <option value=""></option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label for="realization_width">Lebar Roll</label>
                        <select class="form-control" name="realization_width" required>
                          <option value=""></option>
                          @foreach ($widths as $width)
                            @if ($width->width == $data->paper_width)
                              <option value="{{$width->width}}" selected>{{$width->width}}</option>
                            @else
                              <option value="{{$width->width}}">{{$width->width}}</option>
                            @endif
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label for="realization_weight">Realisasi Berat <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" name="realization_weight" class="form-control" value="{{ $data->paper_qty }}" required placeholder="KG" min="0" step="any">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="submit">&nbsp;</label>
                        <div class="form-group text-right">
                          <input type="submit" name="submit" class="btn btn-default" value="Submit">
                        </div>
                      </div>
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
@endsection

@section('script')
  <script type="text/javascript">
    $('document').ready(function(){

      var _string = "{{ $data->paper_key }}";
      var _paperQ = _string.substring(2,4);

      // initialize parsley;
      $('#form').parsley({
        errorsContainer: function(el) {
          return el.$element.closest('.form-group');
        },
      });

      $('[name="po_num"]').change(function(){
  			var _siteId = $('[name="site"]').val();
  			var _poNum = $(this).val();

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
              var _poQty = _dataset.po_qty;
              var _paperQuality = _dataset.purchase_order_details;

              $('[name="supplier_id"]').val(_suppId);
  						$('[name="supplier"]').val(_suppName);
  						$('[name="po_id"]').val(_poId);
              $('[name="total_qty"]').val(_poQty);

              var _str = "<option value=''></option>";
              $.each( _paperQuality, function( key, value ) {
                var _paperQuality = value.paper_quality;
                var _paperGramature = value.paper_gramatures;

                if(_paperQ == _paperQuality){
                  _str = _str + '<option value="'+_paperQuality+'" selected>'+_paperQuality+' - '+_paperGramature+'</option>';
                }
                else{
                  _str = _str + '<option value="'+_paperQuality+'">'+_paperQuality+' - '+_paperGramature+'</option>';
                }
              });
    					$('[name="paper_quality"]').html(_str);
  					}
  					else{
  						alert('PO Tidak Ditemukan');
  						$('[name="po_num"]').focus();
  					}
  				}
  			});
  		});

      $('[name="po_num"]').trigger('change');

      $('[name="paper_quality"]').change(function(){
        var _supplierId = $('[name="supplier_id"]').val();
        var _paperQuality = $('[name="paper_q"]').val();

        $.ajax({
  				type: "POST",
  				url: "{{ route('keys.ajax.getPaperKeyByQuality') }}",
  				data: { _token : '{{ csrf_token() }}', supplier_id : _supplierId, paper_quality : _paperQuality},
  				datatype:"JSON",
  				success: function(data){
  					if(data.status == true){
              var _dataset = data.dataset;

              var _str = "<option value=''></option>";
              $.each( _dataset, function( key, value ) {
                var _paperKey = value.paper_key;
                if(_paperKey == _string){
                  _str = _str + '<option value="'+_paperKey+'" selected>'+_paperKey+'</option>';
                }
                else{
                  _str = _str + '<option value="'+_paperKey+'">'+_paperKey+'</option>';
                }
              });
    					$('[name="realization_key"]').html(_str);
  					}
  					else{
  						alert('Kualitas Kertas Tidak Ditemukan');
  						$('[name="paper_quality"]').focus();
  					}
  				}
  			});
      });

      $('[name="paper_quality"]').trigger('change');

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
