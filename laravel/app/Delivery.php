<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Auth;
class Delivery extends Authenticatable
{
    protected $table = "delivery_boys";

    /*
    |----------------------------------------------------------------
    |   Validation Rules and Validate data for add & Update Records
    |----------------------------------------------------------------
    */

    public function rules($type)
    {
        if($type === 'add')
        {
            return [

            'phone' => 'required|unique:delivery_boys',

            ];
        }
        else
        {
            return [

            'phone'     => 'required|unique:delivery_boys,phone,'.$type,

            ];
        }
    }

    public function validate($data,$type)
    {

        $validator = Validator::make($data,$this->rules($type));
        if($validator->fails())
        {
            return $validator;
        }
    }

    /*
    |--------------------------------
    |Create/Update city
    |--------------------------------
    */

    public function addNew($data,$type)
    {

        if (isset($data['deliveryVia']) && $data['deliveryVia'] == 'user') {
            $id                 = Auth::user()->id;
        }else {
            $id                     =  Admin::find(Auth::guard('admin')->user()->id);
        }

        $add                    = $type === 'add' ? new Delivery : Delivery::find($type);
        $add->store_id          = $id;
        $add->city_id           = isset($data['city_id']) ? $data['city_id'] : 0;
        $add->name              = isset($data['name']) ? $data['name'] : null;
        $add->phone             = isset($data['phone']) ? $data['phone'] : null;
        $add->c_type_staff      = isset($data['c_type_staff']) ? $data['c_type_staff'] : null;
        $add->c_value_staff     = isset($data['c_value_staff']) ? $data['c_value_staff'] : null;
        $add->ruc               = isset($data['ruc']) ? $data['ruc'] : null;
        $add->status            = isset($data['status']) ? $data['status'] : 0;

        if(isset($data['password']))
        {
            $add->password      = bcrypt($data['password']);
            $add->shw_password  = $data['password'];
        }

        $add->save();
    }

    /*
    |--------------------------------------
    |Get all data from db
    |--------------------------------------
    */
    public function getAll($store = 0)
    {
        return Delivery::where(function($query) use($store) {

            if($store > 0)
            {
                $query->where('store_id',$store);
            }

        })->leftjoin('users','delivery_boys.store_id','=','users.id')
          ->leftjoin('city','delivery_boys.city_id','=','city.id')
          ->select('city.name as city','delivery_boys.*')
          ->orderBy('delivery_boys.id','DESC')->get();
    }

   public function login($data)
   {
     $chk = Delivery::where('status',0)->where('phone',$data['phone'])->where('shw_password',$data['password'])->first();

     if(isset($chk->id))
     {
        return [
            'msg' => 'done',
            'user_id' => $chk->id,
            'user_type' => $chk->store_id
        ];
     }
     else
     {
        return ['msg' => 'Opps! Detalles de acceso incorrectos'];
     }
   }

   public function getReport($data)
    {
        $res = Delivery::where(function($query) use($data) {

            if($data['staff_id'])
            {
                $query->where('delivery_boys.id',$data['staff_id']);
            }

        })->join('orders','delivery_boys.id','=','orders.d_boy')
        ->select('orders.store_id as ord_store_id','orders.*','delivery_boys.*')
        ->orderBy('delivery_boys.id','ASC')->get();

       $allData = [];

       foreach($res as $row)
       {

            // Obtenemos el comercio
            $store = User::find($row->ord_store_id);

            $allData[] = [
                'id'                => $row->id,
                'name'              => $row->name,
                'rfc'               => $row->rfc,
                'email'             => $row->email,
                'store'             => $store->name,
                'store_rfc'         => $store->rfc,
                'platform_porcent'  => $row->price_comm,
                'type_staff_porcent'=> ($row->c_type_staff == 0) ? 'Valor Fijo' : 'valor en %',
                'staff_porcent'     => $row->c_value_staff,
                'total'             => $row->total
            ];
       }

       return $allData;
    }
    public function overView()
    {

        $admin = new Admin;

        return [
            'total'     => Order::where('d_boy',$_GET['id'])->count(),
            'complete'  => Order::where('d_boy',$_GET['id'])->where('status',5)->count(),
            'canceled'  => Order::where('d_boy',$_GET['id'])->where('status',2)->count(),
            'x_day'     => [
                'tot_orders' => Order::where('d_boy',$_GET['id'])->whereDate('created_at','LIKE','%'.date('m-d').'%')->count(),
                'amount'     => $this->chartxday($_GET['id'],0,1)['amount'],
                'comm'       => $this->chartxday($_GET['id'],0,1)['comm'],
                'comm_day'   => $this->chartxday($_GET['id'],0,1)['comm_day'],
                'all'        => $this->chartxday($_GET['id'],0,1)
            ],
            'day_data'     => [
                'day_1'    => [
                'data'  => $this->chartxday($_GET['id'],2,1),
                'day'   => $admin->getDayName(2)
                ],
                'day_2'    => [
                'data'  => $this->chartxday($_GET['id'],1,1),
                'day'   => $admin->getDayName(1)
                ],
                'day_3'    => [
                'data'  => $this->chartxday($_GET['id'],0,1),
                'day'   => $admin->getDayName(0)
                ]
            ],
            'week_data' => [
                'total' => $this->chartxWeek($_GET['id'])['total'],
                'amount' => $this->chartxWeek($_GET['id'])['amount']
            ],
            'month'     => [
                'month_1'     => $admin->getMonthName(2),
                'month_2'     => $admin->getMonthName(1),
                'month_3'     => $admin->getMonthName(0),
            ],
            'complet'   => [
                'complet_1'    => $this->chart($_GET['id'],2,1)['order'],
                'complet_2'    => $this->chart($_GET['id'],1,1)['order'],
                'complet_3'    => $this->chart($_GET['id'],0,1)['order'],
            ],
            'cancel'   => [
                'cancel_1'    => $this->chart($_GET['id'],2,1)['cancel'],
                'cancel_2'    => $this->chart($_GET['id'],1,1)['cancel'],
                'cancel_3'    => $this->chart($_GET['id'],0,1)['cancel']
            ]
        ];
    }
    public function chart($id,$type,$sid = 0)
    {
        $month      = date('Y-m',strtotime(date('Y-m').' - '.$type.' month'));

            $order   = Order::where(function($query) use($sid,$id){

                if($sid > 0)
                {
                    $query->where('d_boy',$id);
                }

            })->where('status',5)->whereDate('created_at','LIKE',$month.'%')->count();


            $cancel  = Order::where(function($query) use($sid,$id){

                if($sid > 0)
                {
                    $query->where('d_boy',$id);
                }

            })->where('status',2)->whereDate('created_at','LIKE',$month.'%')->count();

            return ['order' => $order,'cancel' => $cancel];
    }
    public function chartxday($id,$type,$sid = 0)
    {
        $admin = new Admin;
        $date_past = strtotime('-'.$type.' day', strtotime(date('Y-m-d')));
        $day = date('m-d', $date_past);

        $comm = 0;
        $amount = 0;
        $debt = 0;

        $order   = Order::where(function($query) use($sid,$id){

                if($sid > 0)
                {
                    $query->where('d_boy',$id);
                }

            })->where('status',5)->whereDate('created_at','LIKE','%'.$day.'%')->count();


        $cancel  = Order::where(function($query) use($sid,$id){

                if($sid > 0)
                {
                    $query->where('d_boy',$id);
                }

            })->where('status',2)->whereDate('created_at','LIKE','%'.$day.'%')->count();


        if ($type == 0) {
            $i = new OrdenItem;
            $staff = Delivery::find($id);
            $order_day   = Order::where(function($query) use($sid,$id){

                if($sid > 0)
                {
                    $query->where('d_boy',$id);
                }

            })->where('status',5)->whereDate('created_at','LIKE','%'.$day.'%')->get();
            if($order_day->count() > 0) {
            foreach ($order_day as $cm) {
                $staff_comm = $staff->c_value_staff;

                $comm_amount = ($cm->d_charges * $staff_comm) / 100;
                $amount_prev = $cm->d_charges - $comm_amount;

                $amount = $amount + $amount_prev;

                /****************************************************************** */

                $total_store = $i->RealTotal($cm->id);
                $comm_staff = ($cm->d_charges * $staff_comm) /100;

                $comm_store = ($cm->total - $total_store) - $cm->d_charges;
                // lo que debe el repartidor
                $debt = $debt + ($comm_staff + $comm_store);

                /***************************************************************/
                $comm = $staff->amount_acum;
                }
            } else {
                $comm = $comm + $staff->amount_acum;
            }
        }
        return ['order' => $order,'cancel' => $cancel,'comm' => $comm,'comm_day' =>$debt,'amount' => $amount];
    }
    public function chartxWeek($id)
    {
            $date = strtotime(date("Y-m-d"));

            $init_week = strtotime('last Sunday');
            $end_week  = strtotime('next Saturday');

            $total   = Order::where(function($query) use($id){

                $query->where('d_boy',$id);

            })->where('status',5)
                ->where('created_at','>=',date('Y-m-d', $init_week))
                ->where('created_at','<=',date('Y-m-d', $end_week))->count();

            $sum   = Order::where(function($query) use($id){

                $query->where('d_boy',$id);

            })->where('status',5)
                ->where('created_at','>=',date('Y-m-d', $init_week))
                ->where('created_at','<=',date('Y-m-d', $end_week))->sum('d_charges');

            $dboy = Delivery::find($id);

            $comm = ($sum * $dboy->c_value_staff) / 100;

            return [
                'total'   => $total,
                'amount'  => $comm,
                'lastday' => date('Y-m-d', $init_week),
                'nextday' => date('Y-m-d', $end_week)
            ];
    }

    public function add_comm($data, $id)
    {
        $staff = Delivery::find($id);

        $amount_acum = $staff->amount_acum - $data['pay_ataff'];

        $staff->amount_acum = $amount_acum;
        $staff->save();
    }

    public function Commset_delivery($order_id,$d_boy_id)
    {
        $order          = Order::find($order_id);
        $staff          = Delivery::find($d_boy_id);
        $c_value_staff  = $staff->c_value_staff;
        $store          = User::find($order->store_id);
        $i              = new OrderItem;

        $total_order    = $order->total;  // 165
        $total_store    = $i->RealTotal($order->id); //140

        $delivery_charges = $order->d_charger; // Punto C

        $comm_staff = ($delivery_charges * $c_value_staff) / 100; //Punto D

        $comm_store = ($total_order - $total_store) - $delivery_charges;
        // 5
        // lo aud debe el repartidor

        $debt = $comm_staff + $comm_store;

        $amount_acum = $staff->amount_acum + $debt;

        $staff->amount_acum = $amount_acum;
        $staff->save();
    }
}
