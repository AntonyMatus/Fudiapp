@extends('admin.layout.main')

@section('title') Manage Restaurants @endsection

@section('icon') mdi-home @endsection


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
<th>Image</th>
<th>Name</th>
<th>Phone</th>
<th>City</th>
<th>Status</th>
<th style="text-align: right">Option</th>
</tr>

</thead>
<tbody>

@foreach($data as $row)

<tr>
<td width="5%"><img src="{{ Asset('upload/user/'.$row->img) }}" height="30"></td>
<td width="13%">{{ $row->name }}<br><small>{{ $row->Cat }}</small></td>
<td width="12%">{{ $row->phone }}</td>
<td width="14%">{{ $row->city }}</td>
<td width="12%">

@if($row->status == 0)

<button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-info" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Active</button>

@else

<button type="button" class="btn btn-sm m-b-15 ml-2 mr-2 btn-danger" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id) }}')">Disabled</button>

@endif

</td>

<td width="35%" style="text-align: right">


<a href="javascript::void()" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle <?php if($row->open == 1){ echo "btn-danger"; } else { echo "btn-success"; } ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php if($row->open == 1){ echo "Closed Right Now"; } else { echo "Open Right Now"; } ?>" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id.'?type=open') }}')"><i class="mdi mdi-disc"></i></a>

<a href="javascript::void()" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle <?php if($row->trending == 1){ echo "btn-success"; } else { echo "btn-warning"; } ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php if($row->trending == 1){ echo "in Trending"; } else { echo "Make Trending"; } ?>" onclick="confirmAlert('{{ Asset($link.'status/'.$row->id.'?type=trend') }}')"><i class="mdi mdi-trending-up"></i></a>

<a href="{{ Asset(env('admin').'/loginWithID/'.$row->id) }}" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-info" data-toggle="tooltip" data-placement="top" data-original-title="Login as User" target="_blank"><i class="mdi mdi-login"></i></a>

<a href="javascript::void()" class="btn m-b-15 ml-2 mr-2 btn-md  btn-rounded-circle btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="View Login Details" onclick="showMsg('Username : {{ $row->email }}<br>Password : {{ $row->shw_password }}')"><i class="mdi mdi-lock"></i></a>

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