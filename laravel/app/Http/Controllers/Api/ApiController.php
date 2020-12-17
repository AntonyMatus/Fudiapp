<?php namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use App\City;
use App\OfferStore;
use App\Offer;
use App\User;
use App\Cart;
use App\CartCoupen;
use App\AppUser;
use App\Order;
use App\Lang;
use App\Rate;
use App\Slider;
use App\Banner;
use App\Address;
use App\Admin;
use App\Page;
use App\Language;
use App\Text;
use App\Delivery;
use App\CategoryStore;
use App\Opening_times;
use DB;
use Validator;
use Redirect;
use Excel;
use Stripe;
class ApiController extends Controller {


	public function welcome()
	{
		$res = new Slider;

		return response()->json(['data' => $res->getAppData()]);
	}

	public function city()
	{
		$city = new City;
        $text = new Text;
        $lid =  isset($_GET['lid']) && $_GET['lid'] > 0 ? $_GET['lid'] : 0;

		return response()->json(['data' => $city->getAll(0),'text'		=> $text->getAppData($lid)]);
	}

	public function lang()
	{
		$res = new Language;

		return response()->json(['data' => $res->getWithEng()]);
	}

	public function homepage($city_id)
	{
		$banner  = new Banner;
		$store   = new User;
		$text    = new Text;
		$offer   = new Offer;
		$cats    = new CategoryStore;
		$l 		 = Language::find($_GET['lid']);

		$data = [

		'banner'	=> $banner->getAppData($city_id,0),
		'middle'	=> $banner->getAppData($city_id,1),
		'bottom'	=> $banner->getAppData($city_id,2),
		'store'		=> $store->getAppData($city_id),
		'trending'	=> $store->InTrending($city_id), //$store->getAppData($city_id,true),
		'text'		=> $text->getAppData($_GET['lid']),
		'app_type'	=> isset($l->id) ? $l->type : 0,
		'admin'		=> Admin::find(1),
		'Categorys' => $cats->getAllCats(),
		'offers'    => $offer->getAll(0),
		'Tot_stores'=> $store->getTotsStores($city_id)
		];

		return response()->json(['data' => $data]);
	}

	public function GetInfiniteScroll($city_id) {
		
		$store   = new User;
		
		$data = [
			'store'		=> $store->GetAllStores($city_id)
		];

		return response()->json(['data' => $data]);
	}

	public function getTypeDelivery($id)
	{
		$user = new User;
		return response()->json([$user->getDeliveryType($id)]);
	}

	public function search($query,$type,$city)
	{
		$user = new User;

		return response()->json(['data' => $user->getUser($query,$type,$city)]);
	}

	public function SearchCat($city_id)
	{
		$user = new User;
		return response()->json(['cat'=> CategoryStore::find($_GET['cat'])->name,'data' => $user->SearchCat($city_id)]);
	}


	public function addToCart(Request $Request)
	{
		$res = new Cart;

		return response()->json(['data' => $res->addNew($Request->all())]);
	}

	public function updateCart($id,$type)
	{
		$res = new Cart;

		return response()->json(['data' => $res->updateCart($id,$type)]);
	}

	public function cartCount($cartNo)
	{
	  if(isset($_GET['user_id']) && $_GET['user_id'] > 0)
	  {
	  	$order = Order::where('user_id',$_GET['user_id'])->whereIn('status',[0,1,1.5,3,4])->count();
	  }
	  else
	  {
	  	$order = 0;
	  }

	  $cart = new Cart;

	  return response()->json([

	  	'data'  => Cart::where('cart_no',$cartNo)->count(),
	  	'order' => $order,
	  	'cart'	=> $cart->getItemQty($cartNo)

	  	]);
	}

	public function getCart($cartNo)
	{
		$res = new Cart;

		return response()->json(['data' => $res->getCart($cartNo)]);
	}

	public function getOffer($cartNo)
	{
		$res = new Offer;

		return response()->json(['data' => $res->getOffer($cartNo)]);
	}

	public function applyCoupen($id,$cartNo)
	{
		$res = new CartCoupen;

		return response()->json($res->addNew($id,$cartNo));
	}

	public function signup(Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->addNew($Request->all()));
	}

	public function sendOTP(Request $Request)
	{
		$phone = $Request->phone;
		$hash  = $Request->hash;

		return response()->json(['otp' => app('App\Http\Controllers\Controller')->sendSms($phone,$hash)]);
	}


	public function SignPhone(Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->SignPhone($Request->all()));
	}

	public function login(Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->login($Request->all()));
	}

	public function Newlogin(Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->Newlogin($Request->all()));
	}

	public function forgot(Request $Request)
	{
		$res = new AppUser;
		// return response()->json($Request->all());
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

	public function loginFb(Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->loginFb($Request->all()));
	}

	public function getAddress($id)
	{
		$address = new Address;
		$cart 	 = new Cart;

		$data 	 = [
		'address'	 => $address->getAll($id),
		'Comercio'   => User::find($_GET['store']),
		'total'   	 => $cart->getCart($_GET['cart_no'])['total'],
		'c_charges'  => $cart->getCart($_GET['cart_no'])['c_charges']
		];

		return response()->json(['data' => $data]);
	}

	public function addAddress(Request $Request)
	{
		$res = new Address;

		return response()->json($res->addNew($Request->all()));
	}

	public function removeAddress($id)
	{
		$res = new Address;
		return response()->json($res->Remove($id));
	}

	public function searchLocation(Request $Request)
	{
		$city = new City;
		return response()->json([
			'citys' => $city->getAll()
		]);

	}

	public function order(Request $Request)
	{
		$res = new Order;

		return response()->json($res->addNew($Request->all()));
	}

	public function userinfo($id)
	{
		return response()->json(['data' => AppUser::find($id)]);
	}

	public function updateInfo($id,Request $Request)
	{
		$res = new AppUser;

		return response()->json($res->updateInfo($Request->all(),$id));
	}

	public function cancelOrder($id,$uid)
	{
		$res = new Order;

		return response()->json($res->cancelOrder($id,$uid));
	}


	public function rate(Request $Request)
	{
		$rate = new Rate;

		return response()->json($rate->addNew($Request->all()));

	}

	public function pages()
	{
		$res = new Page;

		return response()->json(['data' => $res->getAppData()]);
	}

	public function myOrder($id)
	{
		$res = new Order;

		return response()->json(['data' => $res->history($id)]);
	}

	public function stripe()
	{

		Stripe\Stripe::setApiKey(Admin::find(1)->stripe_api_id);

        $res = Stripe\Charge::create ([
                "amount" => $_GET['amount'] * 100,
                "currency" => "MXN",
                "source" => $_GET['token'],
				"description" => "Pago de compra en FudiFood"
        ]);

        if($res['status'] === "succeeded")
        {
        	return response()->json(['data' => "done",'id' => $res['source']['id']]);
        }
        else
        {
        	return response()->json(['data' => "error"]);
        }
	}

	public function getStatus($id)
	{
		$order = Order::find($id);
		$dboy  = Delivery::find($order->d_boy);
		$store = User::find($order->store_id);

		return response()->json(['data' => $order,'dboy' => $dboy, 'store' => $store]);
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

	public function getChat($id)
	{
		// $chat  = new Chat;
		$op_time = new Opening_times;
		return response()->json(['chat' => $op_time->ViewTime($id)]);
	}

	public function sendChat(Request $Request)
	{
		$chat = new Chat;
		return response()->json($chat->addNew($Request->all()));
	}
}
