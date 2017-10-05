@extends('layouts.main')

@section('title', 'Edit Penerimaan Roll')

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
      <li><a href="{{ route('receiveroll.index') }}">Penerimaan Roll</a></li>
      <li><a href="{{ route('receiveroll.edit', $data->id) }}" class="active">Edit</a></li>
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
              <form id="form" class="form" role="form" method="POST" action="{{ route('receiveroll.update', $data->id) }}">
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
                          <select class="form-control" name="type" disabled>
                            @if ($data->rtype == 'AUTO')
                              <option value="AUTO" selected>AUTOGENERATE</option>
                              <option value="FOX">FOX</option>
                              <option value="BOOKED">BOOKED</option>
                            @elseif ($data->type == 'FOX')
                              <option value="AUTO">AUTOGENERATE</option>
                              <option value="FOX" selected>FOX</option>
                              <option value="BOOKED">BOOKED</option>
                            @else
                              <option value="AUTO">AUTOGENERATE</option>
                              <option value="FOX">FOX</option>
                              <option value="BOOKED" selected>BOOKED</option>
                            @endif
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="site">Site <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="site_id" value="{{ $data->site_id }}">
                          <select class="form-control" name="site" disabled>
                            @foreach ($sites as $site)
                              @if ($site->id == $data->site_id)
                                <option value="{{ $site->id }}" selected>{{ $site->short_name }}</option>
                              @else
                                <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                              @endif
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
                            <input type="text" class="form-control js-datatimepicker" name="receive_date" value="{{ date('Y-m-d', strtotime($data->receive_date)) }}" required>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="verification_date">Tgl Verifikasi <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control js-datatimepicker" name="verification_date"
                            @if (isset($data->verify_roll))
                              value="{{ date('Y-m-d', strtotime($data->verify_roll->verify_date)) }}"
                            @endif
                            disabled>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="po_num">P.O# <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="po_id" value="{{ $data->po_id }}">
                          <input type="text" name="po_num" class="form-control js-change" value="{{ $data->po_num }}" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="supplier">Supplier <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="hidden" name="supplier_id" value="{{ $data->supplier_id }}" required>
                          <input type="text" name="supplier" class="form-control" value="{{ $data->supplier->short_name }}" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="paper_key">Kualitas Kertas</label>
                        <select class="form-control js-change" name="paper_key" required>
                          <option value=""></option>
                          @foreach ($data->supplier->keys as $key)
                            @if ($key->paper_key == $data->paper_key)
                              <option value="{{ $key->paper_key }}" selected>{{ $key->paper_key }}</option>
                            @else
                              <option value="{{ $key->paper_key }}">{{ $key->paper_key }}</option>
                            @endif
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="paper_width">Lebar Roll</label>
                        <select class="form-control js-change" name="paper_width" required>
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
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="supplier_roll_id">Supplier Roll ID</label>
                        <div class="form-group">
                          <input type="text" name="supplier_roll_id" class="form-control" value="{{ $data->supplier_roll_id }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="unique_roll_id">Roll ID <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control" name="unique_roll_id" value="{{ $data->unique_roll_id }}" readonly>
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
                          <input type="number" name="roll_weight" class="form-control" value="{{ $data->roll_weight }}" required placeholder="KG" min="0" step=".01">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="roll_diameter">Diameter Roll <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" name="roll_diameter" class="form-control" value="{{ $data->roll_diameter }}" required placeholder="MM" min="0" step=".01">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="do_num">DO# <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" name="do_num" class="form-control" value="{{ $data->doc_ref }}" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="no_pol">Nomor Polisi <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" name="no_pol" class="form-control" value="{{ $data->wagon }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <div class="form-group">
                          <input type="text" name="remarks" class="form-control" value="{{ $data->remarks }}">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- RIght Side -->
                    <div class="row">
              				<div class="col-md-12">
              					<div class="page-header">
              					  <h3 class="text-right">Record Log</h3>
              					</div>
              				</div>
              			</div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table id="table-history" class="table table-hover table-striped" width="100%">
                            <thead class="f12">
                              <tr>
                                <th class="text-center w25"></th>
                                <th class="text-center w25">Created</th>
                                <th class="text-center w25">Updated</th>
                                <th class="text-center w25">Deleted</th>
                              </tr>
                            </thead>
                	          <tbody class="tbody searchable f12">
                              <!-- Receive -->
                              <tr>
                                <td class="text-center w25">Receiving</td>
                                <td class="text-center w25">{{ $data->created_by }} <br/> {{ $data->created_at }} </td>
                                <td class="text-center w25">
                                  @if ($data->rstatus == 'AM')
                                    {{ $data->updated_by }} <br/> {{ $data->updated_at }}
                                  @endif
                                </td>
                                <td class="text-center w25">{{ $data->deleted_by }} <br/> {{ $data->deleted_at }} </td>
                              </tr>
                              <!-- Verify -->
                              @if ($data->verify_roll != null)
                                <tr>
                                  <td class="text-center w25">
                                    Verify
                                    @ability('superuser,rollstock-spv', 'rollstock-destroy')
                                      </br>
                                      <a href="{{ route('verifyroll.delete', $data->verify_roll->id) }}" target="_blank" onclick="return confirm('Yakin mau hapus verifikasi ini?');">
                                        <i class="f10">Hapus</i>
                                      </a>
                                    @endability
                                    <br/>
                                  </td>
                                  <td class="text-center w25">{{ $data->verify_roll->created_by }} <br/> {{ $data->verify_roll->created_at }} </td>
                                  <td class="text-center w25">
                                    @if ($data->verify_roll->rstatus == 'AM')
                                      {{ $data->verify_roll->updated_by }} <br/> {{ $data->verify_roll->updated_at }}
                                    @endif
                                  </td>
                                  <td class="text-center w25">{{ $data->verify_roll->deleted_by }} <br/> {{ $data->verify_roll->deleted_at }} </td>
                                </tr>

                                <!-- EDI Export -->
                                @if ($data->verify_roll->edi_export_details != null)
                                  @foreach ($data->verify_roll->edi_export_details as $detail)
                                    <tr>
                                      <td class="text-center w25">
                                        EDI EXPORT
                                        <br/>
                                        <a href="{{ route('edi.show', $detail->edi_export_id) }}" target="_blank">
                                          <i class="f10">{{ $detail->edi_counter }}</i>
                                        </a>
                                      </td>
                                      <td class="text-center w25">{{ $detail->created_by }} <br/> {{ $detail->created_at }} </td>
                                      <td class="text-center w25">
                                        @if ($detail->rstatus == 'AM')
                                          {{ $detail->updated_by }} <br/> {{ $detail->updated_at }}
                                        @endif
                                      </td>
                                      <td class="text-center w25">{{ $detail->deleted_by }} <br/> {{ $detail->deleted_at }} </td>
                                    </tr>
                                  @endforeach
                                @endif
                              @endif
                	          </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group text-left">
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

      $('.js-change').change(function(){
        $('[name="generate-roll-id"]').removeAttr('disabled');
        $('[name="unique_roll_id"]').removeAttr('readonly');
        $('[name="unique_roll_id"]').attr('required',true);
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
  				data: { _token : '{{ csrf_token() }}', site_id : _siteId, po_num : _poNum },
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
        var _uroll  = $('[name="unique_roll_id"]').val();
        var _counter = _uroll.substr(_uroll.length - 7);

        switch (_rtype) {
  				case 'FOX':
  				var _suppcode = _uroll.substr(0,1);
  				var _pKey1    = _paperKey.substr(2,1);
  				var _pKey2    = _paperKey.substr(4,3);
  				var _year 	 = _uroll.substr(8,2);

  				var _urollid = _suppcode+_pKey1+_pKey2+(pad((parseInt(_paperWidth)/10),3))+_year+'XXXXX';
  				break;
  				case 'AUTO':
          console.log((pad((parseInt(_paperWidth)/10),3)));
  				var _urollid = _paperKey+(pad((parseInt(_paperWidth)/10),3))+_counter;
  				break;
  				case 'BOOKED':
  				var _urollid = _paperKey+(pad((parseInt(_paperWidth)/10),3))+_counter;
  				break;
  			}

        if(_paperKey!="" && _paperWidth!=""){
  				$('[name="unique_roll_id"]').val(_urollid);
  			}
  			else{
  				alert('Kualitas kertas dan lebar roll tidak boleh kosong.');
  			}

  		});

      $("form").submit(function(){
        var _rtype = $('[name="type"]').val();
        var _rollId = $('[name="unique_roll_id"]').val();
        var _counter = _rollId.substr(_rollId.length - 5);

        if(_counter == 'XXXXX'){
          alert('Roll ID Harus Diisi dengan Format yang Benar.');
          $('[name="unique_roll_id"]').focus();
          return false;
        }
        else if(_rtype == 'FOX' && _rollId.length != 15){
          alert('Roll ID Lebih / Kurang dari 15 Karakter.');
          $('[name="unique_roll_id"]').focus();
          return false;
        }
        else if(_rtype == 'AUTO' && _rollId.length != 17){
  				alert('Roll ID Lebih / Kurang dari 17 Karakter.');
  				$('[name="unique_roll_id"]').focus();
  				return false;
  			}
  			else if(_rtype == 'BOOKED' && _rollId.length != 17){
  				alert('Roll ID Lebih / Kurang dari 17 Karakter.');
  				$('[name="unique_roll_id"]').focus();
  				return false;
  			}
        else{
          return true;
        }

  		});

    });
  </script>
@endsection
