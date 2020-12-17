<div class="row">
<div class="col-lg-12 m-b-30">
<div class="card">
<div class="card-header">
<div class="card-title">Pedidos Recientes</div>

<div class="card-controls">

<a href="#" class="js-card-refresh icon"> </a>

</div>

</div>

<div class="table-responsive">

<table class="table table-hover table-sm ">
<thead>
<tr>
<th>Order ID</th>
<th>Type</th>
<th>User</th>
<th>Address</th>
<th>Status</th>
<th>Order Time</th>
<th class="text-center">Action</th>
</tr>
</thead>
<tbody>
<tr>

@foreach($orders as $row)

<tr>
<td width="10%">#{{ $row->id }}</td>
<td width="20%">
<br>
@if($row->type == 1)

<small style="color:blue">Entrega a domcilio</small>

@else

<small style="color:green">Recogido</small>


@endif
<td width="15%">{{ $row->name }}<br>{{ $row->phone }}</td>
<td width="15%">{{ $row->address }}</td>
<td width="15%">{!! $row->getStatus($row->id) !!}</td>
<td width="15%">{{ date('d-M-Y',strtotime($row->created_at)) }}</td>
<td width="15%">@include('admin.order.action')</td>
@endforeach

</tr>

</tbody>
</table>

</div>
{!! $orders->links() !!}
</div>
</div>
</div>