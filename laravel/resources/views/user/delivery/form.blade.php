
<div class="form-row">
<input type="text" name="deliveryVia" value="user" hidden>
<div class="form-group col-md-12">
<label for="inputEmail6">Nombre</label>
{!! Form::text('name',null,['id' => 'code','class' => 'form-control','required' => 'required'])!!}
</div>
</div>

<div class="form-row">
<div class="form-group col-md-6">
<label for="inputEmail6">Telefono (This will be username)</label>
{!! Form::text('phone',null,['id' => 'code','class' => 'form-control','required' => 'required'])!!}
</div>

<div class="form-group col-md-6">
@if($data->id)
<label for="inputEmail6">Cambiar Contraeña</label>
<input type="password" name="password" class="form-control">
@else
<label for="inputEmail6">Contraseña</label>
<input type="password" name="password" class="form-control" required="required">
@endif
</div>
</div>

<div class="form-row">
	<div class="form-group col-md-6">
	<label for="inputEmail6">Estado</label>
	<select name="status" class="form-control">
		<option value="0" @if($data->status == 0) selected @endif>Active</option>
		<option value="1" @if($data->status == 1) selected @endif>Disbaled</option>
	</select>
	</div>
</div>


<button type="submit" class="btn btn-success btn-cta">Guardar Cambios</button>
