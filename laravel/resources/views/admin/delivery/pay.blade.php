@extends('admin.layout.main')

@section('title') Amortización @endsection

@section('icon') mdi-calendar @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-lg-10 mx-auto  mt-2">
<div class="card py-3 m-b-30">
<div class="card-body">
{!! Form::model($data, ['url' => [$form_url],'files' => true,'method' => 'PATCH'],['class' => 'col s12']) !!}

<div class="form-row">
    <input type="text" name="deliveryVia" value="admin" hidden>
    <div class="form-group col-md-6">
    <label for="inputEmail6">Monto Adeudado</label>
        <input type="text" disabled value="${{number_format($data->amount_acum,2)}}" class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label for="pay_staff">Pagar al Admin</label>
        <input type="number" id="pay_staff" name="pay_staff" placeholder="Ingresa el monto del pago que realizado" class="form-control">
    </div>
</div>
<button type="submit" class="btn btn-success btn-cta">Guardar Cambios</button>
</form>
</div>
</div>
</div>
</div>
</div>
</section>

@endsection
