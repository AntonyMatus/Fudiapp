@extends('admin.layout.main')

@section('title') {{ $title }} @endsection

@section('icon') mdi-cart @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-md-12">
<div class="card py-3 m-b-30">

<div class="row">
<div class="col-md-12" style="text-align: right;"><a href="{{ Asset(env('admin').'/order/add') }}" class="btn m-b-15 ml-2 mr-2 btn-rounded btn-warning">Add New</a>&nbsp;&nbsp;&nbsp;</div>

</div>


<div class="card-body">
<table class="table table-hover ">
<thead>
<tr>
<th>ID</th>
<th>Store</th>
<th>User</th>
<th>Address</th>
<th>Items</th>
<th style="text-align: right">Option</th>
</tr>

</thead>
<tbody>

@foreach($data as $row)

<tr>
<td width="6%">#{{ $row->id }}</td>
<td width="12%">{{ $row->store }}</td>
<td width="12%">{{ $row->name }}<br>{{ $row->phone }}</td>
<td width="15%">{{ $row->address }},{{ $row->city }}</td>
<td width="40%">
	
<div class="row">
<div class="col-md-6"><b>Item</b></div>
<div class="col-md-3"><b>Qty</b></div>
<div class="col-md-3"><b>Price</b></div>
</div><hr>

@foreach($item->getItem($row->id) as $i)

<div class="row" style="font-size: 13px;font-weight:800;">
<div class="col-md-6">{{$i['item'] }}</div>
<div class="col-md-3">{{ $i['qty'] }}</div>
<div class="col-md-3">{{ $currency.$i['price'] }}</div>
</div><hr>

@if(count($item->getAddon($i['id'],$row->id)) > 0)

<div class="row" style="font-size: 12px">
    <div class="col-md-6">Complementos</div>
</div><hr>

@foreach($item->getAddon($i['id'],$row->id) as $add)

<div class="row" style="font-size: 12px">
<div class="col-md-6"><small style="color:blue;font-size:11px;margin-left:10px;">{{ $add->addon }}</small></div>
<div class="col-md-3">{{ $add->qty }}</div>
<div class="col-md-3">{{ $currency.$add->price }}</div>
</div><hr>

@endforeach

@endif

@endforeach

<div class="row" style="font-size: 12px;color:red">
@if($row->type == 1)
<div class="col-md-6">Delivery Charges : {{ $currency.$row->d_charges }}</div>
@else
<div class="col-md-6">No hay cargo de envio</div>
@endif

<div class="col-md-3">Discount : {{ $currency.$row->discount }}</div>
<div class="col-md-3">Total : {{ $currency.$row->total }}</div>
</div><hr>

@if($row->notes)
<small style="color:blue">Notes : {{ $row->notes }}</small>
@endif
</td>

<td width="20%" style="text-align: right">

@include('admin.order.action')

</td>
</tr>

@endforeach

</tbody>
</table>

</div>
</div>

{!! $data->links() !!}

</div>
</div>
</div>
</section>

@endsection