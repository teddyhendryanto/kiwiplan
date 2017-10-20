@extends('layouts.main')

@section('title', 'Realisasi P.O')

@section('pluginscss')
  <!-- Parsley -->
  <link rel="stylesheet" href="{{ asset('css/parsley.css')}}">
@endsection

@section('breadcrumb')
  <div id="#breadcrumb">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Beranda</a></li>
      <li><a href="#">Roll Stock</a></li>
      <li><a href="#">Paper Roll</a></li>
      <li><a href="{{ route('purchase_order_realizations.index') }}">Realisasi P.O</a></li>
    </ol>
  </div>
@endsection

@section('content')
  @include('partials.flash')
  <div class="row">
    <div class="col-md-12">
      <div id="panel-info" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Realisasi P.O</h3>
        </div>
        <div id="panel-body" class="panel-body">
          <div class="row">
            <div class="col-md-12">
              <form id="form-filter" class="form" role="form" method="POST" action="{{ route('purchase_order_realizations.showHistory') }}">
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
                          <select class="form-control" name="site" required>
                            @foreach ($sites as $site)
                              @if ($site->id == env('SITE_ID'))
                                <option value="{{ $site->id }}" selected>{{ $site->short_name }}</option>
                              @else
                                <option value="{{ $site->id }}">{{ $site->short_name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="rsatus">Nomor P.O</label>
                        <div class="form-group">
                          <input type="text" class="form-control" name="po_num" value="" required>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="submit">&nbsp;</label>
                        <div class="form-group">
                          <input type="submit" name="submit" class="btn btn-default" value="Search">
                        </div>
                      </div>
                      <div class="col-md-3 text-right">
                        <label for="new">&nbsp;</label>
                        <div class="form-group">
                          <a href="{{ route('purchase_order_realizations.create') }}" class="btn btn-success ">
                            Buat Baru
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </form>
            </div>
          </div>

          @if(isset($realizations) and count($realizations) > 0)
            <div class="row">
      				<div class="col-md-12">
      					<div class="page-header">
      					  <h3>History Realisasi P.O</h3>
                  <h6><i>{{ $po_num }}</i></h6>
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
                    <!-- Can see price -->
                    <thead class="f12">
                      <tr>
                        <th class="text-center w5">#</th>
                        <th class="text-center w12-5">PO#</th>
                        <th class="text-center w12-5">Supplier</th>
                        <th class="text-center w12-5">Paper <br/> Key</th>
                        <th class="text-center w12-5">Paper <br/> Width</th>
                        <th class="text-center w12-5">P.O Weight <br/> (KG)</th>
                        <th class="text-center w12-5">Rcv Weight <br/> (KG)</th>
                        <th class="text-center w12-5">Outstanding Weight <br/> (KG)</th>
                        <th class="text-center w7-5"></th>
                      </tr>
                    </thead>
                    <tbody class="tbody searchable f12">
                      @php
                        $i = 1;
                        $grand_total_qty = 0;
                        $grand_total_weight = 0;
                        $grand_total_outstanding = 0;
                        $subtotal_qty = 0;
                        $subtotal_weight = 0;
                        $subtotal_outstanding = 0;
                      @endphp
                      @foreach ($realizations as $key => $data)
                        @php
                          $outstanding = $data->paper_qty - $data->roll_weight;
                        @endphp
                        <tr class="">
                          <td class="text-center w5">{{ $i }}.</td>
                          <td class="text-center w12-5">{{ $data->purchase_order->po_num }}</td>
                          <td class="text-center w12-5">{{ $data->purchase_order->supplier->short_name }}</td>
                          <td class="text-center w12-5">{{ $data->paper_key }}</td>
                          <td class="text-center w12-5">{{ $data->paper_width }}</td>
                          <td class="text-right w12-5">{{ number_format($data->paper_qty,2,'.',',') }}</td>
                          <td class="text-right w12-5">{{ number_format($data->roll_weight,2,'.',',') }}</td>
                          <td class="text-right w12-5">{{ number_format($outstanding,2,'.',',')}}</td>
                          <td class="text-center w7-5">
                            <a href="{{ route('purchase_order_realizations.edit', $data->id) }}" class="btn btn-default btn-xs" target="_blank">
                              <i class="fa fa-pencil"></i>
                            </a>
                            <a href="{{ route('purchase_order_realizations.delete', $data->id) }}" class="btn btn-default btn-xs" target="_blank" onclick="return confirm('Yakin mau hapus data ini?');">
                              <i class="fa fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                        @php
                          $i++;
                          $subtotal_qty += $data->paper_qty;
                          $subtotal_weight += $data->roll_weight;
                          $grand_total_qty += $data->paper_qty;
                          $grand_total_weight += $data->roll_weight;
                          if($outstanding > 0){
                            $subtotal_outstanding += $outstanding;
                            $grand_total_outstanding += $outstanding;
                          }
                        @endphp
                        @if (@$realizations[$key+1]['purchase_order_id'] != $data['purchase_order_id'])
                          <tr class="subtotal">
                            <td colspan="5">Subtotal</td>
                            <td class="text-right">{{ number_format($subtotal_qty,2,'.',',') }}</td>
                            <td class="text-right">{{ number_format($subtotal_weight,2,'.',',') }}</td>
                            <td class="text-right">{{ number_format($subtotal_outstanding,2,'.',',') }}</td>
                            <td></td>
                          </tr>
                          @php
                            $subtotal_qty = 0;
                            $subtotal_weight = 0;
                            $subtotal_outstanding = 0;
                          @endphp
                        @endif
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr class="grandtotal">
                        <td colspan="5">Grandtotal</td>
                        <td class="text-right">{{ number_format($grand_total_qty,2,'.',',') }}</td>
                        <td class="text-right">{{ number_format($grand_total_weight,2,'.',',') }}</td>
                        <td class="text-right">{{ number_format($grand_total_outstanding,2,'.',',') }}</td>
                        <td></td>
                      </tr>
                    </tfoot>
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

    });
  </script>
@endsection
