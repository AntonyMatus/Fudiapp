@extends('admin.layout.main')

@section('title') Editar Notificaciones @endsection

@section('icon') mdi-settings @endsection

@section('content')

<section class="pull-up">
    <div class="container">
    <div class="row ">
    <div class="col-lg-12 mx-auto mt-2">

    <form action="{{ $form_url }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="tab-content" id="myTabContent1">

    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

    <div class="card py-3 m-b-30">
    <div class="card-body">


    <h4>Editar Notificaciones</h4>

    <div class="card py-3 m-b-30">
    <div class="card-body">
        <h4>Pedido Confirmado</h4>
    <div class="form-row">
    <div class="form-group col-md-4">

    <label for="asd">Titulo:</label>
    <input type="text" class="form-control" id="asd" name="P_Conf" value=" {{ $data->P_Conf}} ">
    </div>

    <div class="form-group col-md-4">

    <label for="asd">Mensaje:</label>
    <input type="text" class="form-control" id="asd" name="P_Conf_msj" value=" {{ $data->P_Conf_msj}} ">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion: <br></label>
        <select name="P_Conf_status" class="form-control">
        <option value="0" @if($data->P_Conf_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_Conf_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Pedido en Ruta</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
    <label for="asd">Titulo:</label>
    <input type="text" class="form-control" id="asd" name="P_ruta" value="{{ $data->P_ruta}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_ruta_msj" value="{{ $data->P_ruta_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_ruta_status" class="form-control">
        <option value="0" @if($data->P_ruta_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_ruta_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Pedido Cancelado</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
        <label for="asd">Titulo:</label>
        <input type="text" class="form-control" id="asd" name="P_Cancel" value="{{ $data->P_Cancel}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_Cancel_msj" value="{{ $data->P_Cancel_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_Cancel_status" class="form-control">
        <option value="0" @if($data->P_Cancel_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_Cancel_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Repartidor Asignado</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
        <label for="asd">Titulo:</label>
        <input type="text" class="form-control" id="asd" name="P_RepAsig" value="{{ $data->P_RepAsig}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_RepAsig_msj" value="{{ $data->P_RepAsig_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_RepAsig_status" class="form-control">
        <option value="0" @if($data->P_RepAsig_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_RepAsig_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Pedido Entregado</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
    <label for="asd">Titulo:</label>
    <input type="text" class="form-control" id="asd" name="P_Entre" value="{{ $data->P_Entre}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_Entre_msj" value="{{ $data->P_Entre_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_Entre_status" class="form-control">
        <option value="0" @if($data->P_Entre_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_Entre_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Pedido No Entregado</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
        <label for="asd">Titulo:</label>
        <input type="text" class="form-control" id="asd" name="P_noEntre" value="{{ $data->P_noEntre}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_noEntre_msj" value="{{ $data->P_noEntre_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_noEntre_status" class="form-control">
        <option value="0" @if($data->P_noEntre_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_noEntre_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    <h4>Pedido Listo</h4>
    <div class="form-row">
    <div class="form-group col-md-4">
        <label for="asd">Titulo:</label>
        <input type="text" class="form-control" id="asd" name="P_List" value="{{ $data->P_List}}">
    </div>
    <div class="form-group col-md-4">
        <label for="asd">Mensaje:</label>
        <input type="text" class="form-control" id="asd" name="P_List_msj" value="{{ $data->P_List_msj}}">
    </div>
    <div class="form-group col-md-4">
        <label for="inputEmail6">Tipo de Notificacion:</label>
        <select name="P_List_status" class="form-control">
        <option value="0" @if($data->P_List_status == 0) selected @endif>Sistema</option>
        <option value="1" @if($data->P_List_status == 1) selected @endif>Personalizado</option>
        </select>
    </div>
    </div>
    </div>
    </div>

    </div>
    </div>
    <button type="submit" class="btn btn-success btn-cta">Save changes</button>
    </form>
    </div>
    </div>
    </div>
    </div>

    </section>

@endsection

