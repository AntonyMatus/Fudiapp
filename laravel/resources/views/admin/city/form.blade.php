
<div class="tab-content" id="myTabContent1">
<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

<div class="form-row">

@include('admin.city.google')

<div class="form-group col-md-6">
<label for="inputEmail6">Status</label>
<select name="status" class="form-control">
	<option value="0" @if($data->status == 0) selected @endif>Active</option>
	<option value="1" @if($data->status == 1) selected @endif>Disbaled</option>
</select>
</div>
</div>
</div>

<button type="submit" class="btn btn-success btn-cta">Save changes</button>
