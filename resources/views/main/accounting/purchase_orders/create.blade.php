@extends('layouts.main')

@section('title', 'New Purchase Order')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Bootstrap Datetimepicker -->
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Accounting</a></li>
      <li><a href="{{ route('purchase_orders.index') }}">Purchase Order</a></li>
      <li><a href="{{ route('purchase_orders.create') }}" class="active">Buat Baru</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <a href="{{ route('purchase_orders.index') }}" class="btn btn-default">
            Back
          </a>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form" class="form" role="form" method="POST" action="{{ route('purchase_orders.store') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-6">
                    <!-- Left Section -->
                    <div class="row">
                      <div class="col-md-6">
                        <label for="type">Tipe <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="type" required autofocus>
                            <option value=""></option>
                            <option value="NORMAL" selected>NORMAL</option>
                            <option value="KHUSUS">KHUSUS</option>
                            <option value="OVERRIDE">OVERRIDE</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="site">Site <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="site" required autofocus>
                            <option value=""></option>
                            @foreach ($sites as $site)
                              <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="po_num_ex">P.O Num Ex</label>
                        <div class="form-group">
                          <input type="text" id="po_num_ex" name="po_num_ex" class="form-control" disabled value="{{ old('po_num_ex') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_num">P.O Num <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" id="po_num" name="po_num" class="form-control" readonly value="{{ old('po_num') }}">
                          <input type="hidden" id="po_num_counter" name="po_num_counter" readonly value="{{ old('po_num_counter') }}">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="po_date">P.O Date <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control js-datatimepicker" name="po_date" value="{{ old('po_date') }}" required>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="supplier">Supplier <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" id="supplier" name="supplier" required>
                            <option value=""></option>
                            @foreach ($suppliers as $supplier)
                              <option value="{{ $supplier->id }}">{{ $supplier->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Right Section -->
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <label for="po_qty">P.O Quantity <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" id="po_qty" name="po_qty" class="form-control" required value="{{ old('po_qty') }}" min="0">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="term">Payment Term</label>
                        <div class="form-group">
                          <input type="text" id="term" name="term" class="form-control" value="{{ old('term') }}" placeholder="Hari">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="contact_person">Contact Person</label>
                        <div class="form-group">
                          <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ old('contact_person') }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="due_date">Delivery Due Date</label>
                        <div class="form-group">
                          <input type="text" id="due_date" name="due_date" class="form-control" value="SEGERA">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <div class="form-group">
                          <input type="text" id="remarks" name="remarks" class="form-control" value="{{ old('remarks') }}">
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div id="result" class="row">
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table id="table-with-inputs" class="table table-hover" width="100%">
                        <thead>
      	                  <tr>
      	                    <th class="text-center w10">Quality</th>
      	                    <th class="text-center w15">Gramatur</th>
      											<th class="text-center w15">Width</th>
      	                    <th class="text-center w10">Qty</th>
                            <th class="text-center w10">UM</th>
      											<th class="text-center w10">Price</th>
                            <th class="text-center w10">Tax</th>
      	                    <th class="text-center w20">Remarks</th>
      	                  </tr>
      	                </thead>
                        <tbody class="tbody searchable f13">
                          @for ($i=0; $i < 10 ; $i++)
                            <tr>
    		                      <td>
    														<select class='form-control js-pquality' name='pquality[{{$i}}]'>
    															<option></option>
                                  @foreach ($qualities as $quality)
                                    <option value="{{ $quality->quality }}">{{ $quality->quality }}</option>
                                  @endforeach
    														</select>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pgram' name='pgram[{{$i}}]' value='' autocomplete='off'>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pwidth' name='pwidth[{{$i}}]' value='' autocomplete='off'>
    		                      </td>
    		                      <td>
    		                        <input type='text' class='form-control text-right' name='pqty[{{$i}}]' value='' autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='pum[{{$i}}]'>
    															<option></option>
    															<option value='TON' selected>TON</option>
    															<option value='ROLL'>ROLL</option>
    														</select>
    		                      </td>
    													<td>
    		                        <input type='number' class='form-control text-right' name='pprice[{{$i}}]' value='' min="0" autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='ptax[{{$i}}]'>
    															<option></option>
    															<option value='EXCLUDE' selected>EXCLUDE</option>
    															<option value='INCLUDE'>INCLUDE</option>
    														</select>
    													</td>
    		                      <td>
    		                        <input type='text' class='form-control js-remarks' name='premarks[{{$i}}]' value='' autocomplete='off' maxlength='50'>
    		                      </td>
    		                    </tr>
                          @endfor
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
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
  <!-- Select2 -->
  <script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
@endsection

@section('script')
  <script type="text/javascript">
    function getLastPONumberBefore(_siteId,_poNumEx){

      $.ajax({
        type: "POST",
        url: "{{ route('purchase_orders.ajax.getLastPONumberBefore') }}",
        data: { _token : '{{ csrf_token() }}', site_id : _siteId, po_num_ex : _poNumEx},
        datatype:"JSON",
        success: function(data){
          if(data.status == true){
            var _data = data.dataset;

            var _ponum = _data.po_num;
            var _ncounter = _data.counter;
						var _supplier = _data.supplier_id;

						$('#po_num').val(_ponum+'X');
            $('#po_num_counter').val(_ncounter);
						$('#supplier').val(_supplier);

            // trigger supplier change
            $('[name="supplier"]').trigger('change');
          }
          else{
            alert('Data tidak ditemukan.');
						$('#po_num_ex').focus();
          }
        }
      });
    }

    $('document').ready(function(){
      $('.js-datatimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        keepOpen: false
      });

      $('.select2').select2();

      // initialize parsley;
      $('#form').parsley();

      $('[name="type"]').change(function(){
  			var _type = $(this).val();

  			if(_type == 'KHUSUS' || _type == 'OVERRIDE'){
  				$('#po_num_ex').removeAttr('disabled');
  				$('#po_num_ex').attr('required','required');
  				$('#po_num').val('');
  				$('#po_num_counter').val('');
  			}
  			else{
  				$('#po_num_ex').attr('disabled',true);
  				$('#po_num_ex').removeAttr('required');
  				$('#po_num').val('');
  				$('#po_num_counter').val('');
  			}
  		});

      $('[name="site"]').change(function(){
        var _type = $('[name="type"]').val();
        var _siteId = $(this).val();

        if(_type == 'KHUSUS'){
          $('#po_num_ex').focus();
        }
        else{
          $.ajax({
            type: "POST",
            url: "{{ route('purchase_orders.ajax.getLastPONumber') }}",
            data: { _token : '{{ csrf_token() }}', site_id : _siteId},
            datatype:"JSON",
            success: function(data){
              var _ponum = data.ponum;
              var _ncounter = data.newcounter;
              $('#po_num').val(_ponum);
              $('#po_num_counter').val(_ncounter);
              $('#supplier').val('');
              $('#contact_person').val('');
              $('#term').val('');
            }
          });
        }
  		});

      $('[name="po_num_ex"]').change(function(){
        var _type = $('[name="type"]').val();
        var _siteId = $('[name="site"]').val();
        var _poNumEx = $('#po_num_ex').val();
        if( _poNumEx == ""){
          alert('Nomor PO sebelumnya tidak boleh kosong.');
          $(this).focus();
        }
        else{
          if(_type == "KHUSUS"){
            getLastPONumberBefore(_siteId,_poNumEx);
          }
        }
      })

      $('[name="supplier"]').change(function(){
        var _supplierId = $(this).val();

        $.ajax({
  				type: "POST",
  				url: "{{ route('purchase_orders.ajax.getSupplierDetail') }}",
  				data: { _token : '{{ csrf_token() }}', supplier_id : _supplierId},
  				datatype:"JSON",
  				success: function(data){
  					console.log(data);

  					var _status = data.status;

  					if(_status == true){
  						var _dataset = data.dataset;

  						var _contactPerson = _dataset.contact_person;
  						var _term = _dataset.term;
  						var _currency = _dataset.currency;

  						$('#contact_person').val(_contactPerson);
  						$('#term').val(_term);

  						if(_currency != "IDR"){
  							$('#remarks').val('USANCE LC 90 DAYS FROM B/L DATA. DETAIL AS ATTACHED IN ORDER CONFIRMATION.');
  						}
  						else{
  							$('#remarks').val('UKURAN MENYUSUL');
  						}

              // getFrequentlyItems
              $('table input').val('');
              $('.js-pquality').val('');
              var _i = 0;
              var _frequents = _dataset.purchase_order_frequents;
              $.each( _frequents, function( key, value ) {
                // console.log('key = '+key);
                // console.log(value);
                var _id = _frequents[key].id;
                var _quality = _frequents[key].paper_quality;
                var _grammatur = _frequents[key].paper_gramatures;
                var _remark = _frequents[key].remarks;

                $('[name="pquality['+_i+']"]').val(_quality);
                $('[name="pgram['+_i+']"]').val(_grammatur);
                $('[name="premarks['+_i+']"]').val(_remark);
                _i = _i+1;
    					});
            }
  					else{
  						alert('Supplier tidak ditemukan!').
  						$('#submit').attr('disabled','disabled');
  					}
  				}
  			});
      });

    });
  </script>
@endsection
