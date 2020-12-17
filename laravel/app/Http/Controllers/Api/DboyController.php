<?php namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use App\Delivery;
use App\Order;
use App\Language;
use App\Order_staff;
use App\Text;
use App\Admin;
use DB;
use Validator;
use Redirect;
use Excel;
use Stripe;
class DboyController extends Controller {

	public function homepage()
	{
		$res 	 = new Order;
		$text    = new Text;
		$l 		 = Language::find($_GET['lid']);

		return response()->json([

		'data' 		=> $res->history(0),
		'text'		=> $text->getAppData($_GET['lid']),
		'app_type'	=> isset($l->id) ? $l->type : 0,
		'admin'		=> Admin::find(1)
		
		]);
	}

	public function homepage_ext()
	{
		$res 	 = new Order;
		$text    = new Text;
		$l 		 = Language::find($_GET['lid']);

		return response()->json([

		'data' 		=> $res->history_ext(0),
		'text'		=> $text->getAppData($_GET['lid']),
		'app_type'	=> isset($l->id) ? $l->type : 0,
		'admin'		=> Admin::find(1)
		
		]);
	}


	public function staffStatus($type)
	{
		$res 			= Delivery::find($_GET['user_id']);
		$res->status 	= $type;
		$res->save();

		return response()->json(['data' => true]);
	}

	public function login(Request $Request)
	{
		$res = new Delivery;
		
		return response()->json($res->login($Request->all()));
	}

	public function forgot(Request $Request)
	{
		$res = new AppUser;
		
		return response()->json($res->forgot($Request->all()));
	}

	public function verify(Request $Request)
	{
		$res = new AppUser;
		
		return response()->json($res->verify($Request->all()));
	}

	public function updatePassword(Request $Request)
	{
		$res = new AppUser;
		
		return response()->json($res->updatePassword($Request->all()));
	}

	public function startRide()
	{
		$res 		 = Order::find($_GET['id']);
		$res->status = $_GET['status'];
		
		$res->save();
		// Verificamos si existe en notficaciones externas
		if (isset($_GET['d_boy'])) {
			$order_Ext = Order_staff::where('order_id',$_GET['id'])->first();
		}else {
			$order_Ext = Order_staff::where('order_id',$_GET['id'])->get();
		}
		
		// Enviamos la notificacion de entrega
		if ($_GET['status'] == 3) {
			// Verificamos si el pedido ya fue tomado
			if ($res->d_boy != 0) {
				// el pedido fue tomado
				return response()->json(['data' => 'order_inrute']);		
			}else {
				// Notificamos al comercio que el repartidor acepto el pedido
				app('App\Http\Controllers\Controller')->sendPushS("Repartidor en camino","El repartidor ha aceptado el pedido, y va en camino.",$res->store_id);
				$res->d_boy = $_GET['d_boy'];
				$res->save();

				if ($order_Ext->count() > 0) {
					$order_Ext->d_boy 	= $_GET['d_boy'];
					$order_Ext->status 	= '3';
					$order_Ext->save();
				}
			}
		}
		if ($_GET['status'] == 4) {
			// Notificamos al usuario que su pedido va en camino.
			$res->sendSms($res->id);

			if ($order_Ext->count() > 0) {
				$order_Ext->d_boy 	= $_GET['d_boy'];
				$order_Ext->status 	= '4';
				$order_Ext->save();
			}
		}else if ($_GET['status'] == 5) {
			if ($order_Ext->count() > 0) {
				Order_staff::where('order_id',$_GET['id'])->delete();
			}else {
				$staff = Delivery::find($res->d_boy);
				$staff->status_send = 0;
				$staff->save();
			}

			$res->sendSms($res->id);
		}
		return response()->json(['data' => 'done']);			
	}

	public function userInfo($id)
	{
		$count = Order::where('d_boy',$id)->where('status',5)->count();

		return response()->json(['data' => Delivery::find($id),'order' => $count]);
	}

	
	public function updateInfo(Request $Request)
	{
		$res 				= Delivery::find($Request->get('id'));
		$res->password      = bcrypt($Request->get('password'));
        $res->shw_password  = $Request->get('password');
		$res->save();

		return response()->json(['data' => true]);
	}

	public function updateLocation(Request $Request)
	{
		if($Request->get('user_id') > 0)
		{
			$add 			= Delivery::find($Request->get('user_id'));
			$add->lat 		= $Request->get('lat');
			$add->lng 		= $Request->get('lng');
			$add->save();
		}

		return response()->json(['data' => true]);
	}


	public function getPolylines()
	{
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$_GET['latOr'].",".$_GET['lngOr']."&destination=".$_GET['latDest'].",".$_GET['lngDest']."&mode=driving&key=".Admin::find(1)->ApiKey_google;
		$max      = 0;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec ($ch);
        $info = curl_getinfo($ch);
        $http_result = $info ['http_code'];
        curl_close ($ch);


		$request = json_decode($output, true);
		
		return response()->json($request);
	}

}