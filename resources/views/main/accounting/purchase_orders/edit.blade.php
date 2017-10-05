@extends('layouts.main')

@section('title', 'Edit Purchase Order')

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
      <li><a href="{{ route('purchase_orders.edit', Request::segment(3)) }}" class="active">Edit</a></li>
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
              <form id="form" class="form" role="form" method="POST" action="{{ route('purchase_orders.update', $data->id) }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-6">
                    <!-- Left Section -->
                    <div class="row">
                      <div class="col-md-6">
                        <label for="site">Site <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control" name="site" disabled>
                            <option value=""></option>
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
                        <label for="po_num_ex">P.O Num Ex</label>
                        <div class="form-group">
                          <input type="text" id="po_num_ex" name="po_num_ex" class="form-control" disabled value="{{ $data->po_num_ex }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_num">P.O Num <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="text" id="po_num" name="po_num" class="form-control" disabled value="{{ $data->po_num }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_date">P.O Date <span class="text-red">*</span></label>
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control js-datatimepicker" name="po_date" value="{{ $data->po_date }}" placeholder="Tanggal Stock" required>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="supplier">Supplier <span class="text-red">*</span></label>
                        <div class="form-group">
                          <select class="form-control select2" name="supplier" disabled>
                            <option value=""></option>
                            @foreach ($suppliers as $supplier)
                              @if ($supplier->id == $data->supplier_id)
                                <option value="{{ $supplier->id }}" selected>{{ $supplier->short_name }}</option>
                              @else
                                <option value="{{ $supplier->id }}">{{ $supplier->short_name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="po_qty">P.O Quantity <span class="text-red">*</span></label>
                        <div class="form-group">
                          <input type="number" id="po_qty" name="po_qty" class="form-control" required value="{{ $data->po_qty }}" min="0">
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Right Section -->
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <label for="term">Payment Term</label>
                        <div class="form-group">
                          <input type="text" id="term" name="term" class="form-control" value="{{ $data->term }}" placeholder="Hari">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="contact_person">Contact Person</label>
                        <div class="form-group">
                          <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ $data->contact_person }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="due_date">Delivery Due Date</label>
                        <div class="form-group">
                          <input type="text" id="due_date" name="due_date" class="form-control" value="{{ $data->due_date }}">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="remarks">Remarks</label>
                        <div class="form-group">
                          <input type="text" id="remarks" name="remarks" class="form-control" value="{{ $data->remarks }}">
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
      	                    <th class="text-center w18">Remarks</th>
                            <th class="text-center w2"></th>
      	                  </tr>
      	                </thead>
                        <tbody class="tbody searchable f13">
                          @php
                            $i = 0;
                          @endphp
                          @foreach ($data->purchase_order_details as $detail)
                            <tr>
    		                      <td>
                                <input type="hidden" name="detail_id[{{$i}}]" value="{{$detail->id}}">
    														<select class='form-control js-pquality' name='pquality[{{$i}}]'>
    															<option></option>
                                  @foreach ($qualities as $quality)
                                    @if ($quality->quality == $detail->paper_quality)
                                      <option value="{{ $quality->quality }}" selected>{{ $quality->quality }}</option>
                                    @else
                                      <option value="{{ $quality->quality }}">{{ $quality->quality }}</option>
                                    @endif
                                  @endforeach
    														</select>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pgram' name='pgram[{{$i}}]' value='{{ $detail->paper_gramatures }}' autocomplete='off'>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pwidth' name='pwidth[{{$i}}]' value='{{ $detail->paper_width }}' autocomplete='off'>
    		                      </td>
    		                      <td>
    		                        <input type='text' class='form-control text-right' name='pqty[{{$i}}]' value='{{ $detail->paper_qty }}' autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='pum[{{$i}}]'>
    															<option></option>
                                  @if ($detail->um == "TON")
                                    <option value='TON' selected>TON</option>
      															<option value='ROLL'>ROLL</option>
                                  @else
                                    <option value='TON'>TON</option>
      															<option value='ROLL' selected>ROLL</option>
                                  @endif
    														</select>
    		                      </td>
    													<td>
    		                        <input type='number' class='form-control text-right' name='pprice[{{$i}}]' value='{{ $detail->paper_price }}' min="0" autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='ptax[{{$i}}]'>
    															<option></option>
                                  @if ($detail->tax == "EXCLUDE")
                                    <option value='EXCLUDE' selected>EXCLUDE</option>
      															<option value='INCLUDE'>INCLUDE</option>
                                  @else
                                    <option value='EXCLUDE'>EXCLUDE</option>
      															<option value='INCLUDE' selected>INCLUDE</option>
                                  @endif
    														</select>
    													</td>
    		                      <td>
    		                        <input type='text' class='form-control js-remarks' name='premarks[{{$i}}]' value='{{ $detail->remarks }}' autocomplete='off' maxlength='50'>
    		                      </td>
                              <td>
                                <button type="button" class="btn btn-block" name="btn-delete" onclick="deleteSingle({{ $detail->id }})"><i class="fa fa-times"></i>
                                </button></td>
    		                    </tr>
                            @php
                              $i++;
                            @endphp
                          @endforeach
                          @for ($j=$i; $j < 10 ; $j++)
                            <tr>
    		                      <td>
    														<select class='form-control js-pquality' name='pquality[{{$j}}]'>
    															<option></option>
                                  @foreach ($qualities as $quality)
                                    <option value="{{ $quality->quality }}">{{ $quality->quality }}</option>
                                  @endforeach
    														</select>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pgram' name='pgram[{{$j}}]' value='' autocomplete='off'>
    		                      </td>
    													<td>
    		                        <input type='text' class='form-control js-pwidth' name='pwidth[{{$j}}]' value='' autocomplete='off'>
    		                      </td>
    		                      <td>
    		                        <input type='text' class='form-control text-right' name='pqty[{{$j}}]' value='' autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='pum[{{$j}}]'>
    															<option></option>
    															<option value='TON' selected>TON</option>
    															<option value='ROLL'>ROLL</option>
    														</select>
    		                      </td>
    													<td>
    		                        <input type='number' class='form-control text-right' name='pprice[{{$j}}]' value='' min="0" autocomplete='off'>
    		                      </td>
                              <td>
    														<select class='form-control' name='ptax[{{$j}}]'>
    															<option></option>
    															<option value='EXCLUDE' selected>EXCLUDE</option>
    															<option value='INCLUDE'>INCLUDE</option>
    														</select>
    													</td>
    		                      <td>
    		                        <input type='text' class='form-control js-remarks' name='premarks[{{$j}}]' value='' autocomplete='off' maxlength='50'>
    		                      </td>
                              <td></td>
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
    function deleteSingle(_id){
      if(confirm('Yakin ingin hapus data ini?')){
        console.log(_id);
        $.ajax({
          url : "{{ route('purchase_orders.ajax.deleteDetailSingle') }}",
          type: "POST",
          data : {_token : '{{ csrf_token() }}', id : _id},
          dataType: "JSON",
          success: function(data)
          {
						console.log(data);

						if(data.status == true){
							alert('Hapus Data Berhasil');
	            location.reload();
						}
						else{
							alert('Hapus Data Gagal');
						}
          },
          error: function(data){
            alert('Error Delete Data')
          }
        });
      }
    }

    $('document').ready(function(){
      $('.js-datatimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        keepOpen: false
      });

      $('.select2').select2();

      // initialize parsley;
      $('#form').parsley();

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

      $('[name="site"]').change(function(){
  			var _siteId = $(this).val();
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
  		});

    });
  </script>
@endsection
