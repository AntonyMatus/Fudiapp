<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Auth;
use Mail;
use DB;
class Order_staff extends Authenticatable
{
   protected $table = 'orders_staff';

    /*
    |--------------------------------
    |Create/Update Orders Ext
    |--------------------------------
    */

    public function addNew($order_id,$type)
    {

        $add               = $type === 'add' ? new Order_staff : Order_staff::where('order_id',$order_id)->first();
        
        $add->order_id     = $order_id;
        $add->d_boy        = 0;
        $add->status       = 1;
        $add->save();
    }
}

?>