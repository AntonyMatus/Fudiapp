@extends('admin.layout.main')

@section('title') Administrar personal de entrega @endsection

@section('icon') mdi-account-clock @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-md-12">
<div class="card py-3 m-b-30">

<div class="row">
<div class="col-md-12" style="text-align: right;"><a href="{{ Asset($link.'add') }}" class="btn m-b-15 ml-2 mr-2 btn-rounded btn-warning">Add New</a>&nbsp;&nbsp;&nbsp;</div>

</div>


<div class="card-body">
<table class="table table-hover ">
<thead>
<tr>
<th>Nombre</th>
<th>Telefono</th>
<th>Contraseña</th>
<th>Ciudad</th>
<th>% Acumulado</th>
<th>Status</th>
<th style="text-align: center">Opciones</th>
</tr>

</thead>
<tbody>

@foreach($data as $row)

@if($row->store_id == 0)
<tr>
<td width="8%">{{ $row->name }}</td>
<td width="6%">{{ $row->phone }}</td>
<td width="6%">{{ $row->shw_password }}</td>
<td width="7%">
    {{$row->city}}
</td>
<td width="15%" align="center">
@if($row->amount_acum > 0)
    ${{$row->amount_acum}}
    @else
    $0
    @endif
</td>
<td width="8%">
    @if($row->status == 0)
        <button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-success" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Active</button>
    @else
        <button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-danger" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Disabled</button>
    @endif
</td>
<td width="25%" style="text-align: left">
    {!! Form::open(['url' => [$form_url],'target' => '_blank']) !!}
        <input type="hidden" name="staff_id" value="{{$row->id}}">
        <input type="hidden" name="type_report" value="excel">

        <button type="submit" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="Descargar Reporte">
            <i class="mdi mdi-cloud-download"></i>
        </button>

        <a href="{{ Asset($link.$row->id.'/edit') }}" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Editar Repartidor"><i class="mdi mdi-border-color"></i></a>
        <a href="{{ Asset($link.$row->id.'/pay') }}" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="Agregar Pago"><i class="mdi mdi-cash-usd"></i></a>
        <button type="button" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar Repartidor" onclick="deleteConfirm('{{ Asset($link."delete/".$row->id) }}')"><i class="mdi mdi-delete-forever"></i></button>

    </form>
</td>
</tr>
@endif

@endforeach

</tbody>
</table>

</div>
</div>
</div>
</div>
</div>
</section>

<style>
    .mr-2, .mx-2 {
    margin-right: .3rem !important;
    }
</style>
@endsection

