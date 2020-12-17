@extends('user.layout.main')

@section('title') Upload Excel File @endsection

@section('icon') mdi-silverware-fork-knife @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-lg-10 mx-auto  mt-2">
<div class="card py-3 m-b-30">
<div class="card-body">
{!! Form::open(['url' => [Asset('import')],'files' => true],['class' => 'col s12']) !!}

<div class="form-row">
<div class="form-group col-md-6">
<label for="asd">Select File (<small style="color:red">it will delete all the previous enter you made & replace with new one</small>)</label>
<input type="file" class="form-control" name="file" required="required">
</div>
</div>

<button type="submit" class="btn btn-success btn-cta">Upload</button>

</form>
</div>
</div>
</div>
</div>
</div>
</section>

@endsection