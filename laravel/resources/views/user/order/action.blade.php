@include('user.order.dispatch')
@include('user.order.dispatchApp')

@if($row->status == 0)

    <div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $row->id }}" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Options </button>

    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $row->id }}" style="padding: 10px 10px">

    <a href="{{ Asset('orderStatus?id='.$row->id.'&status=1') }}" onclick="return confirm('Are you sure?')">Confirmar Orden</a><hr>

    <a href="{{ Asset('orderStatus?id='.$row->id.'&status=2') }}" onclick="return confirm('Are you sure?')">Cancelar Orden</a><hr>

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

        
        @if($p_staff == 1 || $p_staff == 2)
            <a href="{{ Asset('orderStatus?id='.$row->id.'&status=3&staff_ext=1') }}" onclick="return confirm('Are you sure?')">Asignar Repartidor Externo</a><hr>
        @else
            <a href="javascript::void()" data-toggle="modal" data-target="#slideRightModal{{ $row->id }}">Asignar Repartidor</a><hr>
        @endif
    @endif

    <a href="{{ Asset('order/print/'.$row->id) }}" target="_blank">Print Bill</a><hr>

    <a href="{{ Asset('orderStatus?id='.$row->id.'&status=2') }}" onclick="return confirm('Are you sure?')" style="color:red">Cancelar Orden</a>

    </div>
    </div>
    @endif


@elseif($row->status == 2)

    <span style="font-size: 12px">Cancelado a las <br>{{ $row->status_time }}</span>

    @elseif($row->status == 3)
    <div class="btn-group" role="group">
        <button id="btnGroupDrop{{ $row->id }}" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones </button>
        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $row->id }}" style="padding: 10px 10px">
        @if($p_staff == 1 || $p_staff == 2)
            <a href="{{ Asset('orderStatus?id='.$row->id.'&status=3&staff_ext=2') }}" onclick="return confirm('Are you sure?')">Reasignar Repartidor Externo</a><hr>
        @else 
           <a href="javascript::void()" data-toggle="modal" data-target="#slideRightModal{{ $row->id }}">Reasignar Repartidor Propio</a><hr>
        @endif
        </div>
    </div>
    <br /><hr>
    @if($row->d_boy != 0)
    <div>
        <span style="font-size: 12px">Elegido por {{ $row->dboy }} at<br>{{ $row->status_time }}</span>
        <br />
        <a href="{{ Asset('order/print/'.$row->id) }}" target="_blank">Imprimir factura</a>
    </div>
    @endif

@endif
