@extends('layouts.main')

@section('title', 'Penerimaan Roll')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
  <!-- Date Range Picker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Paper Roll</a></li>
      <li><a href="{{ route('receiveroll.index') }}">Penerimaan Roll</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Penerimaan Roll</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="{{ route('receiveroll.showHistory') }}">
                {{ csrf_field() }}
                @if(isset($data))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row">
                  <div class="col-md-12">
                    <!-- Left side -->
                    <div class="row">
                      <div class="col-md-3">
                        <label for="site">Site</label>
                        <div class="form-group">
                          <select class="form-control" name="site">
                            @foreach ($sites as $site)
                              <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="rsatus">Record Status</label>
                        <div class="form-group">
                          <select class="form-control" name="rstatus">
                            <option value="ALL" selected>All</option>
                            <option value="NW">New</option>
                            <option value="AM">Amend</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 text-right">
                        <label for="new">&nbsp;</label>
                        <div class="form-group">
                          <a href="{{ route('receiveroll.create') }}" class="btn btn-success ">
                            Buat Baru
                          </a>
                          <a href="{{ route('receiveroll.edit.custom') }}" class="btn btn-warning">
                            Search Receiving
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <label for="supplier">Supplier</label>
                        <div class="form-group">
                          <select class="form-control" name="supplier">
                            <option value=""></option>
                            @foreach ($suppliers as $supplier)
                              <option value="{{ $supplier->id }}">{{ $supplier->short_name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="daterange">Period</label>
                        <div class="form-group">
                          <div class="daterange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                          </div>
                          <input type="hidden" name="date_from" value="">
                          <input type="hidden" name="date_to" value="">
                          <input type="hidden" name="count_days" value="">
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

          @if(isset($details))
            <div class="row">
      				<div class="col-md-12">
      					<div class="page-header">
      					  <h3>History Penerimaan Roll</h3>
                  <h6><i>{{ $date_from }} s/d {{ $date_to }}</i></h6>
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
                    @if (Auth::user()->can('accounting-access'))
                      <!-- Can see price -->
                      <thead class="f12">
                        <tr>
                          <th class="text-center w2-5">#</th>
                          <th class="text-center w7-5">Tgl <br/> Receive</th>
                          <th class="text-center w7-5">PO#</th>
                          <th class="text-center w7-5">Supplier</th>
                          <th class="text-center w7-5">Paper <br/> Key</th>
                          <th class="text-center w7-5">Paper <br/> Width</th>
                          <th class="text-center w7-5">Weight <br/> (KG)</th>
                          <th class="text-center w7-5">Diam <br/> (MM)</th>
                          <th class="text-center w12-5">Unique <br/> Roll ID</th>
                          <th class="text-center w12-5">Supplier <br/>Roll ID</th>
                          <th class="text-center w7-5">Doc Ref <br/> Nopol</th>
                          <th class="text-center w7-5">Paper <br/> Price</th>
                          <th class="text-center w5"></th>
                        </tr>
                      </thead>
                      <tbody class="tbody searchable f12">
                        @php
                          $i = 1;
                          $grand_total_weight = 0;
                          $subtotal_weight = 0;
                        @endphp
                        @foreach ($details as $key => $data)
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
                            <td class="text-center w7-5">{{ $data->po_num }}</td>
                            <td class="text-center w7-5">{{ $data->short_name }}</td>
                            <td class="text-center w7-5">{{ $data->paper_key }}</td>
                            <td class="text-center w7-5">{{ $data->paper_width }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_weight,2,'.',',') }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_diameter,2,'.',',') }}</td>
                            <td class="text-center w12-5">{{ $data->unique_roll_id }}</td>
                            <td class="text-center w12-5">{{ $data->supplier_roll_id }}</td>
                            <td class="text-center w7-5">{{ $data->doc_ref }} <br/> {{ $data->wagon }}</td>
                            <td class="text-right w7-5">{{ number_format($data->paper_price,2,'.',',') }}</td>
                            <td class="text-center w5">
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
                            $subtotal_weight += $data->roll_weight;
                            $grand_total_weight += $data->roll_weight;
                          @endphp
                          @if (@$details[$key+1]['doc_ref'] != $data['doc_ref'])
                            <tr class="subtotal">
                              <td colspan="6">Subtotal</td>
                              <td class="text-right">{{ number_format($subtotal_weight,2,'.',',') }}</td>
                              <td colspan="6"></td>
                            </tr>
                            @php
                              $subtotal_weight = 0;
                            @endphp
                          @endif
                        @endforeach
          	          </tbody>
                      <tfoot>
                        <tr class="grandtotal">
                          <td colspan="6">Grandtotal</td>
                          <td class="text-right">{{ number_format($grand_total_weight,2,'.',',') }}</td>
                          <td colspan="6"></td>
                        </tr>
                      </tfoot>
                    @else
                      <!-- Other Department -->
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
                          $grand_total_weight = 0;
                          $subtotal_weight = 0;
                        @endphp
                        @foreach ($details as $key => $data)
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
                            <td class="text-center w10">{{ $data->short_name }}</td>
                            <td class="text-center w7-5">{{ $data->paper_key }}</td>
                            <td class="text-center w7-5">{{ $data->paper_width }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_weight,2,'.',',') }}</td>
                            <td class="text-right w7-5">{{ number_format($data->roll_diameter,2,'.',',') }}</td>
                            <td class="text-center w12-5">{{ $data->unique_roll_id }}</td>
                            <td class="text-center w12-5">{{ $data->supplier_roll_id }}</td>
                            <td class="text-center w10">{{ $data->doc_ref }} <br/> {{ $data->wagon }}</td>
                            @if (Auth::user()->can('accounting-access'))
                              <th class="text-center w15">Paper <br/> Price</th>
                            @else
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
                            @endif
                          </tr>
                          @php
                            $i++;
                            $subtotal_weight += $data->roll_weight;
                            $grand_total_weight += $data->roll_weight;
                          @endphp
                          @if (@$details[$key+1]['doc_ref'] != $data['doc_ref'])
                            <tr class="subtotal">
                              <td colspan="6">Subtotal</td>
                              <td class="text-right">{{ number_format($subtotal_weight,2,'.',',') }}</td>
                              <td colspan="5"></td>
                            </tr>
                            @php
                              $subtotal_weight = 0;
                            @endphp
                          @endif
                        @endforeach
          	          </tbody>
                      <tfoot>
                        <tr class="grandtotal">
                          <td colspan="6">Grandtotal</td>
                          <td class="text-right">{{ number_format($grand_total_weight,2,'.',',') }}</td>
                          <td colspan="5"></td>
                        </tr>
                      </tfoot>
                    @endif
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
  <!-- Moment JS -->
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <!-- Date Range Picker -->
  <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('script')
  <script>
    $(function() {
      var start = moment().startOf('month');
      var end = moment().endOf('month');

      function cb(start, end) {
        $('.daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('[name="date_from"]').val(start.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(end.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(end.diff(start, 'days')+1)
      }

      $('.daterange').daterangepicker({
          startDate: start,
          endDate: end,
          ranges: {
             'Hari Ini': [moment(), moment()],
             'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
             '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
             '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
             'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
             'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
      }, cb);

      cb(start, end);

    });



    $(document).ready(function(){
      $('.daterange').on('hideCalendar.daterangepicker, apply.daterangepicker', function(ev, picker) {
        $('[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
        $('[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
        $('[name="count_days"]').val(picker.endDate.diff(picker.startDate, 'days')+1)
      });

    });
  </script>
@endsection
