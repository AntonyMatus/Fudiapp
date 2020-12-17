@extends('user.layout.main')

@section('title') {{ $title }} @endsection

@section('icon') mdi-cart @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-md-12">
<div class="card py-3 m-b-30">



<div class="card-body">
<table class="table table-hover ">
<thead>
<tr>
<th>ID</th>
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
<td width="12%">{{ $row->name }}<br>{{ $row->phone }}</td>

@if($row->type == 1)
<td width="15%">{{ $row->address }},{{ $row->city }}</td>
@endif
@if($row->type == 2 || $row->type == 7)
<td width="15%">El usuario pasara a recoger el pedido</td>
@endif
<td width="40%">
	
<div class="row">
<div class="col-md-6"><b>Item</b></div>
<div class="col-md-6"><b>Qty</b></div>
<!-- <div class="col-md-3"><b>Price</b></div> -->
</div><hr>

@foreach($item->getItem($row->id) as $i)

<div class="row" style="font-size: 12px">
<div class="col-md-6"><small>{{$i['type']}}</small> - {{$i['item'] }}</div>
<div class="col-md-6">x{{ $i['qty'] }}</div>
<!-- <div class="col-md-3">{{ $currency.$i['price'] }}</div> -->

</div><hr>

@if(count($item->getAddon($i['id'],$row->id)) > 0)

@foreach($item->getAddon($i['id'],$row->id) as $add)

<div class="row" style="font-size: 12px">
<div class="col-md-6">{{ $add->addon }}</div>
<div class="col-md-6">x{{ $add->qty }}</div>
<!-- <div class="col-md-3">{{ $currency.$add->price }}</div> -->
</div><hr>

@endforeach

@endif

@endforeach

<div class="row" style="font-size: 12px;color:red">
<div class="col-md-3">Total : {{ $currency.$item->RealTotal($row->id) }}</div>
</div><hr>

@if($row->notes)
<small style="color:blue">Notes : {{ $row->notes }}</small>
@endif
</td>


<td width="20%" style="text-align: right">

@include('user.order.action')

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