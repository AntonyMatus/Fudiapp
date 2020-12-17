<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\CategoryStore;
use App\User;
use App\Admin;
use DB;
use Validator;
use Redirect;
use IMS;
class CategoryController extends Controller {

	public $folder  = "admin/category.";
	/*
	|---------------------------------------
	|@Showing all records
	|---------------------------------------
	*/
	public function index()
	{					
		$admin = new Admin;
		if($admin->hasperm('Dashboard - Categorias')){
			$res = new CategoryStore;
		
			return View($this->folder.'index',['data' => $res->getAll(),'link' => env('admin').'/category/']);

		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Categorias');
		}
		
	}	
	
	/*
	|---------------------------------------
	|@Add new page
	|---------------------------------------
	*/
	public function show()
	{		
		$admin = new Admin;			
		
		if ($admin->hasperm('Dashboard - Categorias')) {
		return View($this->folder.'add',['data' => new CategoryStore,'form_url' => env('admin').'/category']);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Categorias');
		}	
	
	} 
	
	/*
	|---------------------------------------
	|@Save data in DB
	|---------------------------------------
	*/
	public function store(Request $Request)
	{			
		$data = new CategoryStore;	
		
		if($data->validate($Request->all(),'add'))
		{
			return redirect::back()->withErrors($data->validate($Request->all(),'add'))->withInput();
			exit;
		}

		$data->addNew($Request->all(),"add");
		
		return redirect(env('admin').'/category')->with('message','New Record Added Successfully.');
	}
	
	/*
	|---------------------------------------
	|@Edit Page 
	|---------------------------------------
	*/
	public function edit($id)
	{			
		$admin = new Admin;

		if ($admin->hasperm('Dashboard - Categorias')) {
		
		return View($this->folder.'edit',['data' => CategoryStore::find($id),'form_url' => env('admin').'/category/'.$id]);

		} else  {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Categorias');
		}
	}
	
	/*
	|---------------------------------------
	|@update data in DB
	|---------------------------------------
	*/
	public function update(Request $Request,$id)
	{	
		$data = new CategoryStore;
		
		if($data->validate($Request->all(),$id))
		{
			return redirect::back()->withErrors($data->validate($Request->all(),$id))->withInput();
			exit;
		}

		$data->addNew($Request->all(),$id);
		
		return redirect(env('admin').'/category')->with('message','Record Updated Successfully.');
	}
	
	/*
	|---------------------------------------------
	|@Delete Data
	|---------------------------------------------
	*/
	public function delete($id)
	{
		// Verificamos si la categoria ya ha sido asignada
		$assign = User::where('type',$id);

		if ($assign->count() > 0) {
			return redirect(env('admin').'/category')->with('error','Esta categoria esta asignada a uno o varios comercios.');	
		}else {
			CategoryStore::where('id',$id)->delete();
			return redirect(env('admin').'/category')->with('message','Categoria Eliminada con exito.');
		}
	}

	/*
	|---------------------------------------------
	|@Change Status
	|---------------------------------------------
	*/
	public function status($id)
	{
		$res 			= CategoryStore::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/category')->with('message','Status Updated Successfully.');
	}
}