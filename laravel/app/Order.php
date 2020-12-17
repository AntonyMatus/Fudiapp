<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Auth;
use Mail;
use DB;
class Order extends Authenticatable
{
   protected $table = 'orders';

   public function addNew($data)
   {
      $user                = AppUser::find($data['user_id']);
      $store               = User::find($this->getStore($data['cart_no']));
      $addStatus           = false;
      $latD                = 0;
      $lngD                = 0;

      if(isset($data['address']) && $data['address'] > 0)
      {
          $address = Address::find($data['address']);
          $latD    = $address->lat;
          $lngD    = $address->lng;
      }
      else
      {
          $address = new Address;
          $latD    = $data['lat'];
          $lngD    = $data['lng'];
      }

      if ($data['otype'] == '1') {
         $dataMD    = $store->GetMax_distance($store->lat,$store->lng,$store->distance_max,$latD,$lngD);
         // Envio a domicilio
         if ($dataMD == 0) {
            // No hay servicio
            return ['data' => "Not_service"];
         }else {
           $addStatus = true;
         }
      }else {
         $addStatus = true;
      }


      if ($addStatus == true) {
         $add                 = new Order;
         $add->user_id        = $data['user_id'];
         $add->store_id       = $this->getStore($data['cart_no']);
         $add->name           = $user->name;
         $add->email          = $user->email;
         $add->phone          = $user->phone;
         $add->address        = $address->address;
         $add->lat            = $address->lat;
         $add->lng            = $address->lng;
         $add->type           = isset($data['otype']) ? $data['otype'] : 1;
         $add->price_comm     = $this->getTotal($data['cart_no'],$data['otype'],$data['lat'],$data['lng'])['Price_comm'];
         $add->d_charges      = $this->getTotal($data['cart_no'],$data['otype'],$data['lat'],$data['lng'])['d_charges'];
         $add->discount       = $this->getTotal($data['cart_no'],$data['otype'],$data['lat'],$data['lng'])['discount'];
         $add->total          = $this->getTotal($data['cart_no'],$data['otype'],$data['lat'],$data['lng'])['total'];

         $add->payment_method = $data['payment'];
         $add->payment_id     = isset($data['payment_id']) ? $data['payment_id'] : 0;

         $add->notes          = isset($data['notes']) ? $data['notes'] : null;
         $add->save();

         $item = new OrderItem;
         $item->addNew($add->id,$data['cart_no']);

         $addon = new OrderAddon;
         $addon->addNew($add->id,$data['cart_no']);

         $admin = Admin::find(1);

         Cart::where('cart_no',$data['cart_no'])->delete();
         CartCoupen::where('cart_no',$data['cart_no'])->delete();

         //send sms to admin
         $msg = " 🎉 Nuevo pedido recibido 🎉 #".$add->id." valor del pedido ".$admin->currency.$add->total;
         $title = "Nuevo pedido recibido!!";

         $store = User::find($add->store_id)->id;

         app('App\Http\Controllers\Controller')->sendPushS($title,$msg,$store);

         //send push to Store
         app('App\Http\Controllers\Controller')->NewPushSend($title,$msg,$store);



         $return = [
            'id'     => $add->id,
            'store'  => User::find($add->store_id)->name,
            'total'  => $add->total,
            'name'   => $add->name,
            'lat'    => $add->lat,
            'lng'    => $add->lng,
         ];

         // Creamos el log

         $dataLog = [
            'user_id'   => $data['user_id'],
            'store_id'  => $add->store_id,
            'log'       => 'El cliente '.$add->name.' Ha realizado un pedido con el ID #'.$add->id,
            'view'      => 1
         ];

         $log = new Logs;
         // Registramos realizado del pedido
         $log->addNew($dataLog);

         $dataLogStore = [
            'user_id'   => $data['user_id'],
            'store_id'  => $add->store_id,
            'log'       => 'El comercio '.User::find($add->store_id)->name.' ha recibido un pedido con el ID #'.$add->id,
            'view'      => 2
         ];

         // Registramos pedido del comercio
         $log->addNew($dataLogStore);

         return ['data' => ['data' => $return,'currency' => $admin->currency]];
      }
   }

   public function getStore($cartNo)
   {
      return Cart::where('cart_no',$cartNo)->first()->store_id;
   }

   public function getTotal($cartNo,$type,$latD,$lngD)
   {

      $cart       = new Cart;
      $item_total = $cart->getTotal($cartNo);
      $d_charges  = $cart->d_charges($item_total,$cartNo,$latD,$lngD);
      $price_comm = Cart::where('cart_no',$cartNo)->first()->price_comm;
      $discount   = CartCoupen::where('cart_no',$cartNo)->sum('amount');

      if ($type == 2) {
         $total      = ($item_total - $discount);
      }else {
         $total      = ($item_total - $discount) + $d_charges['costs_ship'];
      }

      return ['total' => $total,'discount' => $discount,'d_charges' => $d_charges['costs_ship'],'Price_comm' => $price_comm];
   }

   public function history($id)
   {
      $data     = [];
      $currency = Admin::find(1)->currency;

      $orders = Order::where(function($query) use($id){

         if($id > 0)
         {
            $query->where('orders.user_id',$id);
         }

         if(isset($_GET['id']))
         {
            $query->where('orders.d_boy',$_GET['id']);
         }

         if(isset($_GET['status']))
         {
            if($_GET['status'] == 3)
            {
               $query->whereIn('orders.status',[3,4]);
            }
            else
            {
               $query->where('orders.status',5);
            }
         }

      })->join('users','orders.store_id','=','users.id')
         ->leftjoin('delivery_boys','orders.d_boy','=','delivery_boys.id')
         ->select('users.name as store','orders.*','delivery_boys.name as dboy')
         ->orderBy('id','DESC')
         ->get();

      $u = new User;

      foreach($orders as $order)
      {
         $items = [];
         $i     = new OrderItem;

         if($order->status == 0)
         {
            $status = "Pendiente";
         }
         elseif($order->status == 1)
         {
            $status = "Confirmada";
         }
         elseif($order->status == 2)
         {
            $status = "Cancelada";
         }
         elseif($order->status == 3)
         {
            $status = "Elegido para entregar por ".$order->dboy;
         }
         else
         {
            $status = "Entregado en ".$order->status_time;
         }

         $countRate = Rate::where('order_id',$order->id)->where('user_id',$id)->first();

         $data[] = [

         'id'        => $order->id,
         'store'     => User::find($order->store_id),
         'date'      => date('d-M-Y',strtotime($order->created_at))." | ".date('h:i:A',strtotime($order->created_at)),
         'total'     => $order->total,
         'tot_com'   => $i->RealTotal($order->id),
         'd_charges' => $order->d_charges,
         'items'     => $i->getItem($order->id),
         'status'    => $status,
         'st'        => $order->status,
         'stime'     => $order->status_time,
         'sid'       => $order->store_id,
         'hasRating' => isset($countRate->id) ? $countRate->star : 0,
         'currency'  => $currency,
         'user'      => $order,
         'pay'       => $order->payment_method
         ];
      }

      return $data;
   }

   public function history_ext($id)
   {
      $data     = [];
      $currency = Admin::find(1)->currency;

      $orders = Order_staff::where(function($query) use($id){

         if(isset($_GET['id']))
         {
            $query->whereIn('orders_staff.d_boy',[0,$_GET['id']]);
         }

         if(isset($_GET['status']))
         {
            if($_GET['status'] == 1)
            {
               $query->whereIn('orders_staff.status',[1,2,3,4]);
            }
         }

      })->get();

      if ($orders->count() > 0) {

         foreach($orders as $pedido)
         {

         $order = Order::find($pedido->order_id);

         $items = [];
         $i     = new OrderItem;

         if($order->status == 0)
         {
            $status = "Pendiente";
         }
         elseif($order->status == 1)
         {
            $status = "Confirmada";
         }
         elseif($order->status == 2)
         {
            $status = "Cancelada";
         }
         elseif($order->status == 3)
         {
            $status = "Elegido para entregar por ".$order->dboy;
         }
         else
         {
            $status = "Entregado en ".$order->status_time;
         }

         $countRate = Rate::where('order_id',$order->id)->where('user_id',$id)->first();

            $data[] = [
               'id'        => $order->id,
               'store'     => User::find($order->store_id),
               'date'      => date('d-M-Y',strtotime($order->created_at))." | ".date('h:i:A',strtotime($order->created_at)),
               'total'     => $order->total,
               'd_charges' => $order->d_charges,
               'tot_com'   => $i->RealTotal($order->id),
               'items'     => $i->getItem($order->id),
               'st'        => $order->status,
               'stime'     => $order->status_time,
               'sid'       => $order->store_id,
               'hasRating' => isset($countRate->id) ? $countRate->star : 0,
               'currency'  => $currency,
               'user'      => $order,
               'pay'       => $order->payment_method
            ];
         }
      }
      return $data;
   }

   public function getAll($type = null,$store_id = 0)
   {
      $take  = $type ? 15 : "";

      return Order::where(function($query) use($store_id) {

         if(isset($_GET['status']))
         {
            if($_GET['status'] == 1 && !isset($_GET['type']))
            {
               $query->whereIn('orders.status',[1,1.5,3,3.5,4]);
            }
            else
            {
               $query->where('orders.status',$_GET['status']);
            }
         }

         if (isset($_GET['q'])) {
            $query->where('orders.id',$_GET['q'])->orWhere('orders.phone',$_GET['q']);
         }

         if($store_id > 0)
         {
            $query->where('orders.store_id',$store_id);
         }

         if(isset($_GET['type']))
         {
            $query->where('orders.store_id',Auth::user()->id);
         }

      })->join('users','orders.store_id','=','users.id')
        ->leftjoin('delivery_boys','orders.d_boy','=','delivery_boys.id')
        ->select('users.name as store','orders.*','delivery_boys.name as dboy')
        ->orderBy('orders.id','DESC')
        ->take($take)
        ->paginate(50);
   }

   public function getType($id)
   {
      $res = Order::find($id);

      if($res->status_by == 1)
      {
         $return = "Admin";
      }
      elseif($res->status_by == 2)
      {
         $return = "Store";
      }
      elseif($res->status_by == 3)
      {
         $return = "User";
      }

      return $return;
   }

   public function signleOrder($id)
   {
      return Order::join('users','orders.store_id','=','users.id')
                 ->select('users.name as store','orders.*')
                 ->where('orders.id',$id)
                 ->first();
   }

   public function cancelOrder($id,$uid)
   {
      $res              = Order::find($id);
      $res->status      = 2;
      $res->status_by   = 3;
      $res->status_time    = date('d-M-Y').' | '.date('h:i:A');
      $res->save();

      return ['data' => $this->history($res->user_id)];
   }

   public function getReport($data)
   {
      $res = Order::where(function($query) use($data) {

      if(isset($data['from']))
      {
         $from = date('Y-m-d',strtotime($data['from']));
      }
      else
      {
         $from = null;
      }

      if(isset($data['to']))
      {
         $to = date('Y-m-d',strtotime($data['to']));
      }
      else
      {
         $to = null;
      }

      if($from)
      {
         $query->whereDate('orders.created_at','>=',$from);
      }

      if($to)
      {
         $query->whereDate('orders.created_at','<=',$to);
      }

      if($data['store_id'])
      {
         $query->where('orders.store_id',$data['store_id']);
      }

      })->join('app_user','orders.user_id','=','app_user.id')
        ->join('users','orders.store_id','=','users.id')
        ->select('users.name as store','app_user.name as user','orders.*')
        ->orderBy('orders.id','ASC')->get();

      $allData = [];

      foreach($res as $row)
      {


         // Obtenemos datos del comercio
            $city_search = User::find($row->store_id);
            $city_id     = $city_search->city_id;
            $delivery_charges = $city_search->delivery_charges_value;
            $c_type      = $city_search->c_type;
            $c_value     = $city_search->c_value;
            $city        = City::find($city_id);

         // Obtenemos los datos del usuario
            $user_search = AppUser::find($row->user_id);
            $user_email  = $user_search->email;
            $user_phone  = $user_search->phone;

         // Obtenemos el Type
            if ($row->type == 2) {
               $type = 'Recogido en tienda';
            }else {
               $type = 'Enviado a domicilio';
            }

         // Obtenemos el status
            if ($row->status == 1) {
               $status = "Pedido Confirmado";
               $status_pay = 'Pago Pendiente';
            }else if ($row->status == 3) {
               $status = "Pedido En Curso";
               $status_pay = 'Pago Pendiente';
            }else if ($row->status == 5) {
               $status = "Pedido Entregado";
               $status_pay = 'Pago completo';
            }else {
               $status = "Pedido Cancelado";
               $status_pay = "Pago no realizado";
            }

         // Obtenemos el repartidor
            if ($row->d_boy == 0) {
               $type_staff = 'Repartidor no asignado';
               $name_staff = 'Repartidor no asignado';
            }else {
               $staff         = Delivery::find($row->d_boy);
               $name_staff    = $staff->name;
               if ($staff->name == 0) {
                  $type_staff = "Repartidor de Chefitoo";
               }else {
                  $type_staff = "Repartidor de Comercio";
               }
            }

         // Costo de la compra
            if ($c_type == 1) { // Es %
               $commision = '%'.$c_value;
            }else { // es precio fijo
               $commision = '$'.$c_value;
            }

         // Monto de la comision
            if ($c_type == 1) {
               $amount_comm = ($row->total * $c_value) / 100;
            }else {
               $amount_comm = $c_value;
            }

         // Monto con el cupon aplicado
            $cup_amount = $row->total - $row->discount;
         // Costo de comision del envio

         // % o cantidad de envio por admin
            if (Admin::find(1)->c_type == 0) {
               $costs_ship = '$'.Admin::find(1)->c_value;
               $amount_ship = ($row->d_charges + Admin::find(1)->c_value);
            }else {
               $costs_ship = '%'.Admin::find(1)->c_value;
               $amount_ship = (($row->d_charges * Admin::find(1)->c_value) / 100);
            }


         // Tipo de pago
            if ($row->payment_method == 1) {
               $payment = "Efectivo";
            }elseif ($row->payment_method == 2) {
               $payment = "PayPal";
            }elseif ($row->payment_method == 3) {
               $payment = "Stripe";
            }else {
               $payment = 'Undefined';
            }
         // Cantidad de productos
         $item    = OrderItem::where('order_id',$row->id)->get();

         $items = OrderItem::where('order_item.id',$row->id)
         ->join('item','item.id','=','order_item.item_id')
         ->select('item.name','order_item.price','order_item.qty')->get();

         $allData[] = [
         'id'     => $row->id,
         'date'   => $row->created_at,//date('d-M-Y H:M:S',strtotime($row->created_at)),
         'city'   => isset($city->name) ? $city->name : null,
         'user'   => $row->user,
         'email'  => $user_email,
         'phone'  => $user_phone,
         'store'  => $row->store,
         'type'   => $type,
         'addr'   => $row->address,
         'status' => $status,
         'type_staff' => $type_staff,
         'name_staff' => $name_staff,
         'tot_prods'  => $item->count(),
         'amount' => $row->total,
         'comm_amount' => $amount_comm,
         'commision' => $commision,
         'cupon_value'  => $row->discount,
         'cupon_amount' => $cup_amount,
         'amount_ship'  => $amount_ship,
         'costs_ship'   => $costs_ship,
         'amount_send'  => $row->d_charges,
         'payment' => $payment,
         'pay_status' => $status_pay,
         'productos' => $items
         ];
      }

      return $allData;
   }

   public function getStatus($id)
   {
      $order = Order::find($id);

         if($order->status == 0)
         {
            $status = "<span class='badge badge-soft-danger badge-light'>Pendiente</span>";
         }
         elseif($order->status == 1)
         {
            $status = "<span class='badge badge-soft-info badge-light'>Confirmada</span>";
         }
         elseif($order->status == 2)
         {
            $status = "<span class='badge badge-soft-warning badge-light'>Cancelada</span>";
         }
         elseif($order->status == 3)
         {
            $status = "<span class='badge badge-soft-info badge-light'>Repartidor Asignado</span>";
         }
         elseif($order->status == 4)
         {
            $status = "<span class='badge badge-soft-info badge-light'>No encontrado en domiclio</span>";
         }
         else
         {
            $status = "<span class='badge badge-soft-success badge-light'>Entregado</span>";
         }

         return $status;
   }

   public function sendSms($id)
   {
      $order = Order::find($id);
      $admin = Admin::find(1);
      $comerce = User::find($order->store_id);
      $log = new Logs;
      if($order->status == 1)
      {

        if($order->type == 7) {
            $msg = "Hola! ".$order->name.", Tu orden #".$order->id." está lista para recoger 😃";
            $title = "Pedido Listo.";

            // Registramos el Log
            $dataLog = [
               'user_id'   => $order->user_id,
               'store_id'  => $order->store_id,
               'log'       => 'El Comercio '.$comerce->name.' Termino de preparar el pedido #'.$order->id,
               'view'      => 2
            ];
            $log->addNew($dataLog);
         }else {
            $msg = "Hola! ".$order->name.", 😁 Tu pedido #".$order->id." ha sido confirmado, El total a pagar es de ".$admin->currency.$order->total;
            $title = "Orden Confirmada";
         }

      }
      elseif ($order->status == 1.5) {

         $msg = "Hola! ".$order->name.", Estamos buscando un socio repartidor para tu pedido.";
         $title = "Buscando Repartidores.";
         $msg2 = "El pedido #".$order->id." Te esta esperando 😉 , toca para más información";

         // app('App\Http\Controllers\Controller')->sendPushD("Nuevo pedido recibido",$msg2,$order->d_boy);
         app('App\Http\Controllers\Controller')->sendPushEx("Nuevo pedido recibido",$msg2);
      }
      elseif($order->status == 2)
      {
         $msg   = "Hola! ".$order->name.", 😁 Tu orden #".$order->id." ha sido cancelada :( Lamentamos lo sucedido, porfavor contactanos si en algo podemos ayudarte.";
         $title = "Orden Cancelada";

      }
      elseif($order->status == 3)
      {
         $msg = "Hola ".$order->name.", 😁 se ha asignado un repartidor para tu pedido #".$order->id;
         $title = "Repartidor asignado.";
      }
       elseif($order->status == 4)
      {
         $msg = "No desesperes!! Tu Pedido #".$order->id." Esta en ruta!! 😃";
         $title = "Pedido en ruta";
      }
      elseif ($order->status == 5) {
         $msg = "🎉 Entregamos tu pedido🎉😃, ayudanos recomendandonos, no te olvides de calificar el comercio y 🏡 #QuedateEnCasa 🏡";
         $title = "Pedido entregado";

         if($order->payment_method == 1) {
            $pay_type = "Pago En Efectivo";
         }else if ($order->payment_method == 2) {
            $pay_type = "Pago Via PayPal";
         }else {
            $pay_type = "Pago Via Stripe";
         }
         // Se envia notificacion al administrador
         $para       =   $order->email;
         $asunto     =   'Entregamos tu pedido';
         $mensaje    =   "Hemos entregado tu pedido<br />";
         $mensaje   .=   "<br />Recibo #".$order->id."<br /> <hr /><br />";
         $mensaje   .=   "Total de compra: ".$admin->currency.$order->total."<br />";
         $mensaje   .=   "Metodo de pago:".$pay_type;
         $mensaje   .=   "<br /><br /><hr> ayudanos recomendandonos, no te olvides de calificar el comercio y #QuedateEnCasa.";
         $cabeceras = 'From: soporte.desarrollosqv@gmail.com' . "\r\n";

         $cabeceras .= 'MIME-Version: 1.0' . "\r\n";

         $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
         @mail($para, $asunto, utf8_encode($mensaje), $cabeceras);

         // Registramos el Log

         if ($order->type == 7) {
            $logText = 'El pedido #'.$order->id.' ha sido entregado y el cliente paso a recoger.';
         }else {
            $logText = 'El pedido #'.$order->id.' ha sido entregado en el domicilio especificado.';
         }

         $dataLog = [
            'user_id'   => $order->user_id,
            'store_id'  => $order->store_id,
            'log'       => $logText,
            'view'      => 2
         ];
         $log->addNew($dataLog);
      }
      elseif ($order->status == 6) {
         $msg = "Hola! ".$order->name.", Tu orden #".$order->id." no pudo ser entregada 😒, comunicate con el comercio para más información!!";
         $title = "Pedido no entregado.";

         $dataLog = [
            'user_id'   => $order->user_id,
            'store_id'  => $order->store_id,
            'log'       => 'El pedido #'.$order->id.' ha sido cancelado.',
            'view'      => 2
         ];
         $log->addNew($dataLog);
      }

      if ($title) {
         app('App\Http\Controllers\Controller')->sendPush($title,$msg,$order->user_id);
      }
      return true;
   }

   public function storeOrder($status = null)
   {
      $res = Order::where(function($query) use($status){

         if(isset($_GET['id']))
         {
            $query->where('orders.store_id',$_GET['id']);
         }

         if(isset($_GET['status']) && !$status)
         {
            if($_GET['status'] == 0)
            {
               $query->whereIn('orders.status',[0,1,1.5,3,4]);
            }
         }

         if($status == 5)
         {
            $query->where('orders.status',5);
         }

      })->orderBy('orders.id','DESC')
        ->get();

        $data   = [];
        $admin  = Admin::find(1);
        $item   = new OrderItem;

        foreach($res as $row)
        {

          $data[] = [

          'id'       => $row->id,
          'name'     => $row->name,
          'phone'    => $row->phone,
          'address'  => $row->address,
          'status'   => $row->status,
          'd_boy'    => $row->d_boy,
          'total'    => $row->total,
          'd_charges' => $row->d_charges,
          'real_total' => $item->RealTotal($row->id),
          'currency' => $admin->currency,
          'items'    => $item->getItem($row->id),
          'pay'      => $row->payment_method,
          'date'     => date('d-M-Y',strtotime($row->created_at)),
          'type'     => $row->type,
          'notes'    => $row->notes
          ];
        }

        return $data;
   }

   public function overView()
   {
      $total = Order::where('store_id',$_GET['id'])->count();
      $comp  = Order::where('store_id',$_GET['id'])->where('status',5)->count();

      return ['total' => $total,'complete' => $comp];
   }

   public function getUnit($id)
   {
      $item = Item::find($id);

      $data = [];

      if($item->small_price)
      {
         $data[] = ['id' => 1,'name' => "Small - Rs.".$item->small_price];
      }

      if($item->medium_price)
      {
         $data[] = ['id' => 2,'name' => "Medium - Rs.".$item->medium_price];
      }

      if($item->large_price)
      {
         $data[] = ['id' => 3,'name' => "Large/Full - Rs.".$item->large_price];
      }

      return $data;
   }

   public function editOrder($data,$id)
   {
         $order                     = $id > 0 ? Order::find($id) : new Order;
         $address                   = $data['address'];

         if($id == 0)
         {
            $check = AppUser::where('phone',$data['phone'])->first();

            if(isset($check->id))
            {
               $uid = $check->id;
            }
            else
            {
               $user              = new AppUser;
               $user->name        = isset($data['name']) ? $data['name'] : null;
               $user->phone       = isset($data['phone']) ? $data['phone'] : null;
               $user->store_id    = isset($data['store_id']) ? $data['store_id'] : null;//User::orderBy('id','DESC')->first()->id;
               $user->lat         = isset($data['lat']) ? $data['lat'] : null;
               $user->lng         = isset($data['lng']) ? $data['lng'] : null;
               $user->password    = 123456;
               $user->save();

               $uid = $user->id;
            }
         }

         $order->name               = isset($data['name']) ? $data['name'] : null;
         $order->phone              = isset($data['phone']) ? $data['phone'] : null;
         $order->store_id           = isset($data['store_id']) ? $data['store_id'] : null;//User::orderBy('id','DESC')->first()->id;
         $order->lat                = isset($data['lat']) ? $data['lat'] : null;
         $order->lng                = isset($data['lng']) ? $data['lng'] : null;
         $order->email              = "none";
         $order->address            = $address;

         if($id == 0)
         {
            $order->user_id         = $uid;
         }

         $order->status             = 1;
         $order->type               = 1;
         $order->d_charges          = 0;
         $order->discount           = 0;
         $order->total              = 0;
         $order->save();

         $item = new OrderItem;
         $item->editOrder($data,$order->id);

         $this->updateTotal($order->id);
   }

   public function updateTotal($id)
   {
      $order  = Order::find($id);
      $item   = new OrderItem;
      $total  = $item->getTotal($id);

      if($order->extra == 0)
      {
         $total = $total + $order->extra_amount;
      }
      else
      {
         $total = $total - $order->extra_amount;
      }

      $d_charges = $this->getDelivery($total,$id);

      $total = $total + $d_charges;

      $order->total        = $total;
      $order->d_charges    = $d_charges;
      $order->save();
   }

   public function getDelivery($total,$id)
   {
      $order = Order::find($id);
      $user  = User::find($order->store_id);
      $val   = 0;

      if($user->delivery_charges_value > 0)
      {
         if($user->min_cart_value > 0)
         {
            if($total < $user->min_cart_value)
            {
               $val = $user->delivery_charges_value;
            }
         }
         else
         {
            $val = $user->delivery_charges_value;
         }
      }
      else
      {
         $val = 0;
      }

      return $val;
   }
}
