@extends('admin.layout.main')

@section('title') Editar Comercio @endsection

@section('icon') mdi-comment-plus @endsection


@section('content')

<section class="pull-up">
<div class="container">
<div class="row ">
<div class="col-lg-10 mx-auto  mt-2">
{!! Form::model($data, ['url' => [$form_url],'files' => true,'method' => 'PATCH'],['class' => 'col s12']) !!}

@include('admin.user.form')

</form>
</div>
</div>
</div>
</div>
</div>
</section>

@endsection