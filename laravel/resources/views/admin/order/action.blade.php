@include('admin.order.dispatch')


@if($row->status == 0)

    <div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $row->id }}" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones </button>

    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $row->id }}" style="padding: 10px 10px">

    <a href="{{ Asset(env('admin').'/order/edit/'.$row->id) }}">Editar Pedido</a><hr>
    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=1') }}" onclick="return confirm('Are you sure?')">Confirmar Pedido</a><hr>

    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=2') }}" onclick="return confirm('Are you sure?')">Cancelar Pedido</a><hr>

    </div>
    </div>


@elseif($row->status == 1)

    @if(!$row->dboy)
    <div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $row->id }}" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones </button>

    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $row->id }}" style="padding: 10px 10px">

    <a href="{{ Asset(env('admin').'/order/edit/'.$row->id) }}">Editar Pedido</a><hr>

    @if($row->type == 2)
    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=7') }}" onclick="return confirm('Are you sure?')">Listo para entregar</a><hr>
    @elseif($row->type == 7)
    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=5') }}" onclick="return confirm('Are you sure?')">Entregar Pedido</a><hr>
    @else
    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=1.5&staff_ext=1') }}" onclick="return confirm('Are you sure?')">Asignar Repartidor</a><hr>
    @endif

    <a href="{{ Asset(env('admin').'/order/print/'.$row->id) }}" target="_blank">Imprimir Recibo</a><hr>

    <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=2') }}" onclick="return confirm('Are you sure?')" style="color:red">Cancelar Pedido</a>

    </div>
    </div>
    @endif

    
@elseif($row->status == 1.5)

<span style="font-size: 12px">Asignando Repartidor...</span><hr />
<div class="btn-group" role="group">
    <img src="https://i.pinimg.com/originals/2a/6b/65/2a6b651433f3c6ece42ba25439f76c0d.gif" alt="Buscando..." width="100%" style="border-radius:20px;">
</div>
<div>
</div>


@elseif($row->status == 2)

    <span style="font-size: 12px">Cancelado a las <br>{{ $row->status_time }}</span>

@elseif($row->status == 3)

<span style="font-size: 12px">Repartidor en camino...</span><hr />
<div class="btn-group" role="group">
    <img src="https://media3.giphy.com/media/Pj19z6yzCpa1oQEvhe/giphy-downsized.gif" alt="en camino" width="50%">
</div>
<div>
</div>

@elseif($row->status == 3.5)

<div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $row->id }}" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $row->id }}" style="padding: 10px 10px">
        <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=3&staff_ext=1') }}" onclick="return confirm('Are you sure?')">Asignar Repartidor</a><hr>
        <a href="{{ Asset(env('admin').'/orderStatus?id='.$row->id.'&status=2') }}" onclick="return confirm('Are you sure?')">Cancelar Pedido</a>
        <a href="{{ Asset(env('admin').'/order/print/'.$row->id) }}" target="_blank">Imprimir factura</a>
    </div>
</div>
<hr>
<div >
<span style="font-size: 12px">Asignado a {{ $row->dboy }} a las:<br>{{ $row->status_time }}</span>
</div>
@endif