<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\City;
use App\UserImage;
use App\Admin;
use App\CategoryStore;
use App\Opening_times;
use DB;
use Validator;
use Redirect;
use IMS;
class UserController extends Controller {

	public $folder  = "admin/user.";
	/*
	|---------------------------------------
	|@Showing all records
	|---------------------------------------
	*/
	public function index()
	{					
		$admin = new Admin;
        $city = Auth::guard('admin')->user()->city_id;

		if($admin->hasperm('Adminisrtar Restaurantes')) {
           
                $res = new User;
		        return View($this->folder.'index',['data' => $res->getAll(),'link' => env('admin').'/user/']);
		}else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Adminisrtar Restaurantes');
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
		if ($admin->hasperm('Adminisrtar Restaurantes')) {
			$city = new City;
			$cats = new CategoryStore;
			$times = new Opening_times;
			return View($this->folder.'add',[

				'data' 		 => new User,
				'type_ship'  => Admin::find(1)->c_type,
				'costs_ship' => Admin::find(1)->c_value,
				'ApiKey'     => Admin::find(1)->ApiKey_google,
				'form_url'  => env('admin').'/user',
				'citys'     => $city->getAll(0),
				'admin'		=> true,
				'types'		=> $cats->getAll(),
				'Update'    => false,
				'times'     => $times->getAll(0),
				'opening_time' => $times
			]);
		}else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Adminisrtar Restaurantes');
		}
	}
	
	/*
	|---------------------------------------
	|@Save data in DB
	|---------------------------------------
	*/
	public function store(Request $Request)
	{			
		$data = new User;

		if($data->validate($Request->all(),'add'))
		{
			return redirect::back()->withErrors($data->validate($Request->all(),'add'))->withInput();
			exit;
		}

		$data->addNew($Request->all(),"add");

		$this->sendPush('Nuevo Comercio agregado ',$Request->get('name'));


		return redirect(env('admin').'/user')->with('message','New Record Added Successfully.');
	}
	
	/*
	|---------------------------------------
	|@Edit Page 
	|---------------------------------------
	*/
	public function edit($id)
	{				
		$admin = new Admin;

		if ($admin->hasperm('Adminisrtar Restaurantes')) {
			$city  = new City;
			$cats  = new CategoryStore;
			$times = new Opening_times;
			return View($this->folder.'edit',[

				'data' 		=> User::find($id),
				'form_url'  => env('admin').'/user/'.$id,
				'type_ship'  => Admin::find(1)->c_type,
				'costs_ship' => Admin::find(1)->c_value,
				'ApiKey'     => Admin::find(1)->ApiKey_google,
				'citys'     => $city->getAll(0),
				'images' 	=> UserImage::where('user_id',$id)->get(),
				'types'		=> explode(",",Admin::find(1)->store_type),
				'admin'		=> true,
				'types'		=> $cats->getAll(),
				'Update'    => true,
				'times'     => $times->getAll($id),
				'opening_time' => $times
			]);
		}else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Adminisrtar Restaurantes');
		}
	}
	
	/*
	|---------------------------------------
	|@update data in DB
	|---------------------------------------
	*/
	public function update(Request $Request,$id)
	{	
		$data = new User;
		
		if($data->validate($Request->all(),$id))
		{
			return redirect::back()->withErrors($data->validate($Request->all(),$id))->withInput();
			exit;
		}

		$data->addNew($Request->all(),$id);
		
		return redirect(env('admin').'/user')->with('message','Record Updated Successfully.');
	}
	
	/*
	|---------------------------------------------
	|@Delete Data
	|---------------------------------------------
	*/
	public function delete($id)
	{
		User::where('id',$id)->delete();
		Opening_times::where('store_id', $id)->delete();

		return redirect(env('admin').'/user')->with('message','Record Deleted Successfully.');
	}

	/*
	|---------------------------------------------
	|@Change Status
	|---------------------------------------------
	*/
	public function status($id)
	{
		$res 			= User::find($id);
		
		if(isset($_GET['type']) && $_GET['type'] == "trend")
		{
			$res->trending 	= $res->trending == 0 ? 1 : 0;
		}
		elseif(isset($_GET['type']) && $_GET['type'] == "open")
		{
			$res->open 	= $res->open == 0 ? 1 : 0;
		}else {
			$res->status = $res->status == 0 ? 1 : 0;
		}

		$res->save();

		return redirect(env('admin').'/user')->with('message','Status Updated Successfully.');
	}

	public function imageRemove($id)
	{
		UserImage::where('id',$id)->delete();

		return redirect::back()->with('message','Deleted Successfully.');
	}

	public function loginWithID($id)
	{
		if(Auth::loginUsingId($id))
		{
		   return Redirect::to('home')->with('message', 'Welcome ! Your are logged in now.');	
		}
		else
		{
			return Redirect::to('login')->with('error', 'Something went wrong.');
		}
		
	}

	public function ViewTime($id)
	{
		$op_time 	= new Opening_times;

		$res        = $op_time->	ViewTime($id);

		return $res;
	}
}