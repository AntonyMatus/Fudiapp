@extends('user.layout.main')

@section('title') Administrar categorías @endsection

@section('icon') mdi-silverware-fork-knife @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-md-12">
<div class="card py-3 m-b-30">

<div class="row">
    <div class="col-md-12" style="text-align: right;">
        <a href="{{ Asset($link.'add') }}" class="btn m-b-15 ml-2 mr-2 btn-rounded btn-warning">Add New</a>&nbsp;&nbsp;&nbsp;
    </div>
</div>


<div class="card-body">

{{ Form::open(['route' => 'search', 'method' =>'GET','class' => 'col s12'])}}
<div class="tab-content" id="myTabContent1">
	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
       
        <div class="form-row">
            
        <div class="form-group col-md-3">
            <label for="inputEmail6">Nombre</label><br />
            <input type="text" name="name" id="name" value="{{$name}}" placeholder="Busca por nombre" class="form-control" autocomplete="off">
        </div>
        
        <div class="form-group col-md-3">
            <label for="inputEmail6">Tipo Menu</label>
            <select name="type" class="form-control" id="type">
                <option value="0" @if($type == 0) selected @endif>De Menú</option>
                <option value="1" @if($type == 1) selected @endif>De Complementos</option>
            </select>
        </div>     
        <div class="form-group col-md-1">
            <button type="submit" class="btn btn-success m-b-15 ml-2 mr-2" style="margin-top:30px;">Buscar</button>
        </div>
        
        <div class="form-group col-md-2">
            <a href="{{ Asset($link) }}" class="btn btn-success m-b-15 ml-2 mr-2" style="margin-top:30px;">Ver todo</a>
        </div>
               
        </div>
       
    </div>
</div>
{{Form::close()}}

<table class="table table-hover ">
<thead>
<tr>
<th>Sort Order</th>
<th>Name</th>
<th>Tipo</th>
<th>Status</th>
<th style="text-align: right">Option</th>
</tr>

</thead>
<tbody>

@foreach($data as $row)
<tr>
<td width="15%">{{ $row->sort_no }}</td>
<td width="20%">{{ $row->name }} 
    @if($row->id_element != '')
    <small>({{$row->id_element}})</small>
    @endif
</td>
<td width="20%">
    @if($row->type == 0)
        De Menú
    @else
        De Complemento
    @endif
</td>
<td width="27%">
@if($row->status == 0)

<button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-success" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Active</button>

@else

<button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-danger" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Disabled</button>

@endif

</td>

<td width="19%" style="text-align: right">

<a href="{{ Asset($link.$row->id.'/edit') }}" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Edit This Entry"><i class="mdi mdi-border-color"></i></a>

<button type="button" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="Delete This Entry" onclick="deleteConfirm('{{ Asset($link."delete/".$row->id) }}')"><i class="mdi mdi-delete-forever"></i></button>


</td>
</tr>

@endforeach

</tbody>
</table>

</div>
</div>
</div>
</div>
</div>
</section>

@endsection