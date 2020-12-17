<div class="tab-content" id="myTabContent1">
<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

<div class="form-row">
<div class="form-group col-md-6">
<label for="inputEmail6">Nombre</label>
{!! Form::text('name',null,['id' => 'code','placeholder' => 'Name','class' => 'form-control'])!!}
</div>

<div class="form-group col-md-6">
<label for="inputEmail6">Status</label>
<select name="status" class="form-control">
	<option value="0" @if($data->status == 0) selected @endif>Active</option>
	<option value="1" @if($data->status == 1) selected @endif>Disbaled</option>
</select>
</div>
</div>

<div class="form-row">
<div class="form-group col-md-6">
<label for="inputEmail6">Orden de clasificación</label>
{!! Form::number('sort_no',null,['id' => 'code','placeholder' => 'Name','class' => 'form-control'])!!}
</div>

<div class="form-group col-md-6">
	<label for="inputEmail6">Imagen Descriptiva (512px * 512px)</label>
	<input type="file" name="img" class="form-control" @if(!$data->id) required="required" @endif>
</div>
</div>
</div>
</div>
<button type="submit" class="btn btn-success btn-cta">Save changes</button>
