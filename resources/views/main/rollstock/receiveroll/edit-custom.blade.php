@extends('layouts.main')

@section('title', 'Search Receiving')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="{{ route('receiveroll.index') }}">Paper Roll</a></li>
      <li><a href="{{ route('receiveroll.edit.custom') }}">Search Receiving</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Search Receiving</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="{{ route('receiveroll.showHistory.custom') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-12">
                    <!-- Left side -->
                    <div class="row">
                      <div class="col-md-3">
                        <label for="search_by">Search By</label>
                        <div class="form-group">
                          <select class="form-control" name="search_by">
                            <option value="unique_roll_id" selected>Unique Roll ID</option>
                            <option value="supplier_roll_id">Supplier Roll ID</option>
                            <option value="doc_ref">Doc Ref</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="unique_roll_id" class="js-label">Unique Roll ID</label>
                        <div class="form-group">
                          <input type="text" class="form-control js-input" name="unique_roll_id" value="" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="submit">&nbsp;</label>
                        <div class="form-group">
                          <input type="submit" name="submit" class="btn btn-default" value="Search">
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </form>
            </div>
          </div>

          @if (isset($search_by))
            <div class="row">
      				<div class="col-md-12">
      					<div class="page-header">
      					  <h3>History Penerimaan Roll</h3>
                  <h6><i>{{ $search_by }} : {{ $filter }}</i></h6>
      					</div>
      				</div>
      			</div>
            <div class="row mb5">
              <div class="col-md-3 col-md-offset-9">
                <div class="input-group"> <span class="input-group-addon">Filter</span>
                  <label class="sr-only" for="search-history">Search</label>
                  <input class="form-control" id="filter" name="search-history" placeholder="Type Here" type="text">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table id="table-history" class="table table-hover table-striped" width="100%">
                    <thead class="f12">
                      <tr>
                        <th class="text-center w2-5">#</th>
                        <th class="text-center w7-5">Tgl <br/> Receive</th>
                        <th class="text-center w10">PO#</th>
                        <th class="text-center w10">Supplier</th>
                        <th class="text-center w7-5">Paper <br/> Key</th>
                        <th class="text-center w7-5">Paper <br/> Width</th>
                        <th class="text-center w7-5">Weight <br/> (KG)</th>
                        <th class="text-center w7-5">Diam <br/> (MM)</th>
                        <th class="text-center w12-5">Unique <br/> Roll ID</th>
                        <th class="text-center w12-5">Supplier <br/>Roll ID</th>
                        <th class="text-center w10">Doc Ref <br/> Nopol</th>
                        <th class="text-center w15"></th>
                      </tr>
                    </thead>
        	          <tbody class="tbody searchable f12">
                      @php
                        $i = 1;
                      @endphp
                      @foreach ($datas as $data)
                        @if ($data->verify == false)
                          @php
                            $rclass = 'danger';
                          @endphp
                        @else
                          @php
                            $rclass = '';
                          @endphp
                        @endif
                        <tr class="{{ $rclass }}">
                          <td class="text-center w2-5">{{ $i }}.</td>
                          <td class="text-center w7-5">{{ date('Y-m-d', strtotime($data->receive_date)) }} <br/> {{ $data->receive_time }} </td>
                          <td class="text-center w10">{{ $data->po_num }}</td>
                          <td class="text-center w10">{{ $data->supplier->short_name }}</td>
                          <td class="text-center w7-5">{{ $data->paper_key }}</td>
                          <td class="text-center w7-5">{{ $data->paper_width }}</td>
                          <td class="text-right w7-5">{{ number_format($data->roll_weight,2,'.',',') }}</td>
                          <td class="text-right w7-5">{{ number_format($data->roll_diameter,2,'.',',') }}</td>
                          <td class="text-center w12-5">{{ $data->unique_roll_id }}</td>
                          <td class="text-center w12-5">{{ $data->supplier_roll_id }}</td>
                          <td class="text-center w10">{{ $data->doc_ref }} <br/> {{ $data->wagon }}</td>
                          <td class="text-center w15">
                            <a href="{{ route('receiveroll.edit', $data->id) }}" class="btn btn-default btn-xs" target="_blank">
                              <i class="fa fa-pencil"></i>
                            </a>
                            @if ($data->verify == false)
                              <a href="{{ route('receiveroll.delete', $data->id) }}" class="btn btn-default btn-xs" target="_blank" onclick="return confirm('Yakin mau hapus penerimaan ini?');">
                                <i class="fa fa-trash"></i>
                              </a>
                            @else
                              <a href="javascript:void(0)" class="btn btn-default btn-xs" target="_blank" onclick="return alert('Roll ini sudah di-verifikasi.');">
                                <i class="fa fa-trash"></i>
                              </a>
                            @endif
                          </td>
                        </tr>
                        @php
                          $i++;
                        @endphp
                      @endforeach
        	          </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection

@section('pluginsjs')
  <!-- Parsley JS -->
  <script src="{{ asset('js/parsley.min.js') }}"></script>
@endsection

@section('script')
  <script>
    $(document).ready(function(){
      $('[name="search_by"]').change(function(){
        var _value = $(this).val();
        console.log(_value);
        $('.js-label').attr('for',_value);
        $('.js-input').attr('name',_value);

        if (_value == 'unique_roll_id') {
          $('.js-label').text('Unique Roll ID');
        }
        else if (_value == 'supplier_roll_id') {
          $('.js-label').text('Supplier Roll ID');
        }
        else {
          $('.js-label').text('Doc Ref');
        }
      });

    });
  </script>
@endsection
