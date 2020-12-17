<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
class OrderItem extends Authenticatable
{
   protected $table = 'order_item';

   public function addNew($id,$cartNo)
   {
      $res = Cart::where('cart_no',$cartNo)->get();
      foreach($res as $row)
      {
        $add                = new OrderItem;
        $add->order_id      = $id;
        $add->item_id       = $row->item_id;
        $add->price         = $row->price;
        $add->qty           = $row->qty;
        $add->qty_type      = $row->qty_type;
        $add->save();
      }
   }

   public function getItem($id)
   {
     $res = OrderItem::join('item','order_item.item_id','=','item.id')
                     ->select('item.name as item','order_item.*')
                     ->where('order_item.order_id',$id)->get();
     $data = [];
     $tot_item = 0;

     foreach($res as $row)
     {
         $it = Item::find($row->item_id);
        if($row->qty_type == 1)
        {
          $type = "Chico";
          $tot_item = $it->small_price * $row->qty;
        }
        elseif($row->qty_type == 2)
        {
          $type = "Mediano";
          $tot_item = $it->medium_price * $row->qty;
        }
        else
        {
          $type = "Grande";
          $tot_item = $it->large_price * $row->qty;
        }

        $u   = new User;
        $lid = isset($_GET['lid']) ? $_GET['lid'] : 0;

        $data[] = [

        'item'    => $u->getLangItem($row->item_id,$lid)['name'],
        'price'   => $row->price,
        'Store_price' => $tot_item,
        'qty'     => $row->qty,
        'type'    => $type,
        'id'      => $row->item_id,
        'addon'   => $this->getAddon($row->item_id,$row->order_id)

        ];
     }

     return $data;
   }

   public function getAddon($id,$oid)
   {
      return OrderAddon::join('addon','order_addon.addon_id','=','addon.id')
                       ->select('addon.name as addon','order_addon.*','addon.price')
                       ->where('order_addon.item_id',$id)
                       ->where('order_addon.order_id',$oid)
                       ->get();
   }

   public function editOrder($data,$id)
   {
       $store_id = Order::find($id)->store_id;
      OrderItem::where('order_id',$id)->delete();

      $item = isset($data['item_id']) ? $data['item_id'] : [];
      $unit = isset($data['unit']) ? $data['unit'] : [];
      $qty  = isset($data['qty']) ? $data['qty'] : [];

      $comm = User::find($store_id)->c_value;
      $comm_type = User::find($store_id)->c_type;

      for($i=0;$i<count($item);$i++)
      {
          $it = Item::find($item[$i]);

          if($unit[$i] == 1)
          {
              if($comm_type == 0){
                $price = $it->small_price + $comm;
              } else {
                $price = ($it->small_price + $comm) / 100 + $it->small_price;
              }

          }
          elseif($unit[$i] == 2)
          {
            if($comm_type == 0){
                $price = $it->medium_price + $comm;
            } else {
                $price = ($it->medium_price + $comm) / 100 + $it->medium_price;
            }
          }
          else
          {
              if($comm_type == 0) {
                $price = $it->large_price + $comm;
              } else {
                $price = ($it->large_price + $comm) / 100 + $it->large_price;
              }
          }

          $add              = new OrderItem;
          $add->order_id    = $id;
          $add->item_id     = $item[$i];
          $add->qty         = $qty[$i];
          $add->qty_type    = $unit[$i];
          $add->price       = $price;
          $add->save();
      }
   }

   public function RealTotal($id)
   {

      $res = OrderItem::where("order_id",$id)->get();
      $addon = OrderAddon::where('order_id',$id)->get();
      $cart = new Cart;
      $total = 0;

    //   foreach ($res as $item) {
    //     $it = Item::find($item->item_id);
        // $store = User::find(Order::find($id)->store_id);

        // $comm  = ($store->c_type == 1) ? $store->c_value : 0;

        // if (isset($it)) {
        //   $item_id = $item->item_id;

        //   $small_price  = (is_numeric($it->small_price)) ? $it->small_price : 0;
        //   $medium_price = (is_numeric($it->medium_price)) ? $it->medium_price : 0;
        //   $large_price  = (is_numeric($it->large_price)) ? $it->large_price : 0;

        //   if ($item->qty_type == 1) {
        //     $total += ($small_price - $comm) * $item->qty;
        //   }elseif ($item->qty_type == 2) {
        //     $total += ($medium_price - $comm) * $item->qty;
        //   }else {
        //     $total += ($large_price - $comm) * $item->qty;
        //   }
        // }else {
        //   $total = 0;
        // }


    //   }

        foreach ($res as $item) {
        $it = Item::find($item->item_id);
        if (isset($it)) {
          if ($item->qty_type == 1) {
            $total += $it->small_price * $item->qty;
          }elseif ($item->qty_type == 2) {
            $total += $it->medium_price * $item->qty;
          }else {
            $total += $it->large_price * $item->qty;
          }
        }else {
          $total = 0;
        }
      }

      if ($addon) {
        foreach($addon as $add)
        {
            $addon_add = Addon::find($add->addon_id);
           if ($addon_add) {
             $total += $addon_add->price;
           }
        }
      }

      return $total;
   }

   public function getTotal($id)
   {
      $count = [];

      foreach($this->detail($id) as $i)
      {
        $count[] = $i->price * $i->qty;
      }

      return array_sum($count);
   }

   public function detail($id)
   {
      return OrderItem::join('item','order_item.item_id','=','item.id')
                     ->select('item.name as item','order_item.*')
                     ->where('order_item.order_id',$id)->get();
   }

}
