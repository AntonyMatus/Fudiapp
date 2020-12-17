
<div class="tab-content" id="myTabContent1">

@foreach(DB::table('language')->orderBy('sort_no','ASC')->get() as $l)

<div class="tab-pane fade show" id="lang{{ $l->id }}" role="tabpanel" aria-labelledby="lang{{ $l->id }}-tab">

<input type="hidden" name="lid[]" value="{{ $l->id }}">

<div class="card py-3 m-b-30">
<div class="card-body">

<div class="form-row">
<div class="form-group col-md-6">
<label for="asd">Coupen Code</label>
{!! Form::text('l_code[]',$data->getSData($data->s_data,$l->id,0),['placeholder' => 'Code','class' => 'form-control'])!!}
</div>

<div class="form-group col-md-6">
<label for="asd">Description</label>
{!! Form::text('l_desc[]',$data->getSData($data->s_data,$l->id,1),['placeholder' => 'Description','class' => 'form-control'])!!}
</div>
</div>

</div>
</div>

</div>
@endforeach

<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

<div class="card py-3 m-b-30">
<div class="card-body">

<div class="form-row">
<div class="form-group col-md-6">
<label for="inputEmail6">Coupen Code</label>
{!! Form::text('code',null,['id' => 'code','class' => 'form-control','required' => 'required'])!!}
</div>
<div class="form-group col-md-6">
<label for="inputEmail6">Description</label>
{!! Form::text('description',null,['id' => 'code','class' => 'form-control','required' => 'required'])!!}
</div>
</div>

<div class="form-row">
<div class="form-group col-md-6">
<label for="inputEmail6">Min Cart Value <small>(optional)</small></label>
{!! Form::number('min_cart_value',null,['id' => 'code','class' => 'form-control'])!!}
</div>

<div class="form-group col-md-6">
<label for="inputEmail6">Discount Type</label>
<select name="type" class="form-control">
	<option value="0" @if($data->type == 0) selected @endif>in %</option>
	<option value="1" @if($data->type == 1) selected @endif>Fixed Amount</option>
</select>
</div>
</div>

<div class="form-row">
<div class="form-group col-md-3">
<label for="inputEmail6">Discount Value</label>
{!! Form::number('value',null,['id' => 'code','class' => 'form-control','required' => 'required'])!!}
</div>

<div class="form-group col-md-3">
<label for="inputEmail6">Status</label>
<select name="status" class="form-control">
	<option value="0" @if($data->status == 0) selected @endif>Active</option>
	<option value="1" @if($data->status == 1) selected @endif>Disbaled</option>
</select>
</div>

<div class="form-group col-md-6">
	<label for="inputEmail6">Imagen Descriptiva</label>
	<input type="file" name="img" class="form-control" required="required">
</div>
</div>


<div class="form-row">
<div class="form-group col-md-12">
<label for="inputEmail6">Select Store</label>
<select name="store[]" class="form-control js-select2" multiple="true">
<option value="">All Store</option>
@foreach($users as $user)
<option value="{{ $user->id }}" @if(in_array($user->id,$array)) selected @endif>{{ $user->name }}</option>
@endforeach
</select>
</div>
</div>
</div>
</div>
</div>
</div>
<button type="submit" class="btn btn-success btn-cta">Save changes</button>
