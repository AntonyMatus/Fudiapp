<?php namespace App\Http\Controllers\User;

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

	public $folder  = "user.order.";
	/*
	|---------------------------------------
	|@Showing all records
	|---------------------------------------
	*/
	public function index()
	{					
		$res = new Order;
		
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

		return View($this->folder.'index',[

			'data' 		=> $res->getAll(null,Auth::user()->id),
			'link' 		=> 'order/',
			'title' 	=> $title,
			'item'		=> new OrderItem,
			'boys'		=> Delivery::where('status',0)->where('store_id',Auth::user()->id)->get(),
			'boysApp'   => Delivery::where('status',0)->where('city_id',Auth::user()->city_id)->where('store_id',0)->get(),
			'p_staff'   => Auth::user()->p_staff,
			'form_url'	=> 'order/dispatched',
			'currency'	=> Admin::find(1)->currency
		]);
	}	

	public function orderStatus()
	{
		$res 				= Order::find($_GET['id']);
		$res->status 		= $_GET['status'];
		$res->status_by 	= 1;
		$res->status_time 	= date('d-M-Y').' | '.date('h:i:A');
		$res->save();

		if ($_GET['status'] == 2) {
			
			Order_staff::where('order_id',$_GET['id'])->delete();

		}

		if (isset($_GET['staff_ext'])) {
			$order_Staff = new Order_staff;
			if ($_GET['staff_ext'] == 2) {
				if ($res->d_boy == 0) {
					$order_Staff->addNew($_GET['id'],'update');
				}else {
					$res->d_boy = 0;
					$order_Staff->addNew($_GET['id'],'add');	
				}
			}else {
				$order_Staff->addNew($_GET['id'],'add');	
			}
		}

		$res->sendSms($_GET['id']);
		return Redirect::back()->with('message','Estado del pedido cambiado correctamente'); 
	}

	public function dispatched(Request $Request)
	{
		$sms 				= new Order;
		$res 				= Order::find($Request->get('id'));

		$chk_ext 			= Order_staff::where('order_id',$res->id)->get();

		if ($chk_ext->count() > 0) {
			Order_staff::where('order_id',$res->id)->delete();
		}
		

		$res->status 		= 3;
		$res->status_by 	= 1;
	
		$res->d_boy 		= $Request->get('d_boy');
		$res->status_time 	= date('d-M-Y').' | '.date('h:i:A');
		$res->save();

		$staff       = Delivery::find($Request->get('d_boy'));
		$staff->status_send = 1;
		$staff->save();

	
		$sms->sendSms($Request->get('id'));

		return Redirect::back()->with('message','Estado del pedido cambiado correctamente'); 
	}

	public function printBill($id)
	{
		$order = new Order;
		$item  = new OrderItem;

		return View('user.order.print',[

		'order' 	=> $order->signleOrder($id),
		'items'		=> $item->getItem($id),
		'currency'	=> Admin::find(1)->currency,
		'it'		=> $item

		]);
	}

	public function edit($id)
	{
		$order = Order::find($id);
		$item  = new OrderItem;

		return View($this->folder.'edit',[

		'data' 		=> $order,
		'item'		=> Item::where('store_id',Auth::user()->id)->get(),
		'detail'	=> $item->detail($id),
		'form_url'	=> 'order/edit/'.$id,
		'users'		=> User::get()
		

		]);
	}

	public function orderItem()
	{
		return View($this->folder.'item',['item' => Item::where('store_id',Auth::user()->id)->get(),'data' => new Order]);
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

		return Redirect('order?status=1')->with('message','Order Edit Successfully');
	}

	public function add()
	{
		return View($this->folder.'add',[

		'data' 		=> new Order,
		'item'		=> Item::get(),
		'form_url'	=> 'order/add',
		'users'		=> User::get()

		]);
	}

	public function _add(Request $Request)
	{
		$order = new Order;

		$order->editOrder($Request->all(),0);

		return Redirect('order?status=1')->with('message','Order Added Successfully');
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
}