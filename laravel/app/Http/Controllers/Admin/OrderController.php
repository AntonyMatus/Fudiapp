<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\City;
use App\Order;
use App\OrderItem;
use App\Delivery;
use App\Admin;
use App\Item;
use App\Order_staff;
use DB;
use Validator;
use Redirect;
use IMS;
class OrderController extends Controller {

	public $folder  = "admin/order.";
	/*
	|---------------------------------------
	|@Showing all records
	|---------------------------------------
	*/
	public function index()
	{					
		$res = new Order;
		if (isset($_GET['status'])) {
		
			if($_GET['status'] == 0)
			{
				$title = "Nuevos pedidos";
			}
			elseif($_GET['status'] == 1)
			{
				$title = "Ordenes en ejecución";
			}
			elseif($_GET['status'] == 2)
			{
				$title = "Órdenes canceladas";
			}
			elseif($_GET['status'] == 3)
			{
				$title = "Pedidos despachados";
			}
			elseif($_GET['status'] == 5)
			{
				$title = "Órdenes completadas";
			}

		}else if (isset($_GET['q'])) {
			$title = "Busqueda de Pedidos";
		}

		$admin = new Admin;

		if ($admin->hasperm('Gestion de pedidos')) {
		return View($this->folder.'index',[

			'data' 		=> $res->getAll(),
			'link' 		=> env('admin').'/order/',
			'title' 	=> $title,
			'item'		=> new OrderItem,
			'boys'		=> Delivery::where('status',0)->where('store_id',0)->get(),
			'form_url'	=> env('admin').'/order/dispatched',
			'currency'	=> Admin::find(1)->currency
		]);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Pedidos');
		}	
	}	

	public function orderStatus()
	{
		$res 				= Order::find($_GET['id']);
		if ($_GET['status'] == 7) {
			$res->type = $_GET['status'];
		}else if ($_GET['status'] == 2) {
			
			Order_staff::where('order_id',$_GET['id'])->delete();
			$res->status 		= $_GET['status'];
		}else {
			$res->status 		= $_GET['status'];
		}

		
		$res->status_by 	= 1;
		$res->status_time 	= date('d-M-Y').' | '.date('h:i:A');
		$res->save();

		if (isset($_GET['staff_ext'])) {
			$order_Staff = new Order_staff;
			if ($_GET['staff_ext'] == 2) {
				$order_Staff->addNew($_GET['id'],'update');	
			}else {
				$order_Staff->addNew($_GET['id'],'add');	
			}
		}
		
		$res->sendSms($_GET['id']);

		return Redirect::back()->with('message','Order Status Changed Successfully'); 
	}

	public function dispatched(Request $Request)
	{
		$res 				= Order::find($Request->get('id'));
		$res->status 		= 3;
		$res->status_by 	= 1;
		
		// Marcamos como libre el antiguo repartidor
		if ($res->d_boy) {
			$staff 						= $res->d_boy;
			$staff_old          		= Delivery::find($staff);
			$staff_old->status_send  	= 0;
			$staff_old->save();
		}
		
		
		$res->d_boy 		= $Request->get('d_boy');
		$res->status_time 	= date('d-M-Y').' | '.date('h:i:A');
		$res->save();
		
		$staff       = Delivery::find($Request->get('d_boy'));
		$staff->status_send = 1;
		$staff->save();
		
		$res->sendSms($Request->get('id'));
		
		return Redirect::back()->with('message','Order Status Changed Successfully'); 
	}

	public function printBill($id)
	{

		$admin = new Admin;

		if ($admin->hasperm('Gestion de pedidos')) {
		$order = new Order;
		$item  = new OrderItem;

		return View('admin.order.print',[

		'order' 	=> $order->signleOrder($id),
		'items'		=> $item->getItem($id),
		'currency'	=> Admin::find(1)->currency,
		'it'		=> $item

		]);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Pedidos');
		}
	}

	public function edit($id)
	{
		$admin = new Admin;

		if ($admin->hasperm('Gestion de pedidos')) {
		$order = Order::find($id);
		$item  = new OrderItem;

		return View($this->folder.'edit',[

		'data' 		=> $order,
		'item'		=> Item::where('store_id',$order->store_id)->get(),
		'detail'	=> $item->detail($id),
		'form_url'	=> env('admin').'/order/edit/'.$id,
		'users'		=> User::get()


		]);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Pedidos');
		}
	}

	public function orderItem()
	{
		$admin = new Admin;

		if ($admin->hasperm('Gestion de pedidos')) {
		return View($this->folder.'item',['item' => Item::where('store_id',$_GET['store_id'])->get(),'data' => new Order]);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Pedidos');
		}
	}

	public function getUnit($id)
	{
		$order = new Order;

		$html = "<select name='unit[]'' class='form-control' required='required'>";

		foreach($order->getUnit($id) as $u)
		{
			$html .= "<option value='".$u['id']."'>".$u['name']."</option>";
		}

		$html .= "</select>";

		return $html;
	}

	public function _edit(Request $Request,$id)
	{
		$order = new Order;

		$order->editOrder($Request->all(),$id);

		return Redirect(env('admin').'/order?status=1')->with('message','Order Edit Successfully');
	}

	public function add()
	{
		$admin = new Admin;

		if ($admin->hasperm('Gestion de pedidos')) {
		return View($this->folder.'add',[

		'data' 		=> new Order,
		'item'		=> Item::get(),
		'form_url'	=> env('admin').'/order/add',
		'users'		=> User::get()

		]);
		} else {
			return Redirect::to(env('admin').'/home')->with('error', 'No tienes permiso de ver la sección Pedidos');
		}
	}

	public function _add(Request $Request)
	{
		$order = new Order;

		$order->editOrder($Request->all(),0);

		return Redirect(env('admin').'/order?status=1')->with('message','Order Added Successfully');
	}

	public function getUser($phone)
	{
		$user = Order::where('phone',$phone)->first();

		if(isset($user->id))
		{
			$array = ['phone' => $user->phone,'name' => $user->name,'address' => $user->address,'lat' => $user->lat, 'lng' => $user->lng];
		}
		else
		{
			$array = [];
		}

		return $array;
	}

	public function getCity($id,$type)
	{
		echo $type;
	}
}