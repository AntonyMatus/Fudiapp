<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Auth;
use DB;
class User extends Authenticatable
{
    /*
    |----------------------------------------------------------------
    |   Validation Rules and Validate data for add & Update Records
    |----------------------------------------------------------------
    */
    
    public function rules($type)
    {
        if($type === "add")
        {
            return [

            'name'      => 'required',
            'phone'     => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|min:6',

            ];
        }
        else
        {
            return [

            'name'      => 'required',
            'phone'    => 'required',
            'email'     => 'required|unique:users,email,'.$type,
            
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
    |Create/Update user
    |--------------------------------
    */

    public function addNew($data,$type)
    {

        $a                          = isset($data['lid']) ? array_combine($data['lid'], $data['l_name']) : [];
        $b                          = isset($data['lid']) ? array_combine($data['lid'], $data['l_address']) : [];
        $add                        = $type === 'add' ? new User : User::find($type);
        $add->name                  = isset($data['name']) ? $data['name'] : null;
        $add->phone                 = isset($data['phone']) ? $data['phone'] : null;
        $add->email                 = isset($data['email']) ? $data['email'] : null;
        $add->status                = isset($data['status']) ? $data['status'] : 0;
        $add->city_id               = isset($data['city_id']) ? $data['city_id'] : 0;
        $add->address               = isset($data['address']) ? $data['address'] : 0;
        $add->delivery_time         = isset($data['delivery_time']) ? $data['delivery_time'] : null;
        $add->person_cost           = isset($data['person_cost']) ? $data['person_cost'] : null;
        $add->Cuenta_clave          = isset($data['Cuenta_clave']) ? $data['Cuenta_clave'] : null;
        $add->banco_name            = isset($data['banco_name']) ? $data['banco_name'] : null;
        $add->lat                   = isset($data['lat']) ? $data['lat'] : null;
        $add->lng                   = isset($data['lng']) ? $data['lng'] : null;
        $add->type                  = isset($data['store_type']) ? $data['store_type'] : null;
        $add->min_cart_value        = isset($data['min_cart_value']) ? $data['min_cart_value'] : null;
        
        $add->c_type                = isset($data['c_type']) ? $data['c_type'] : 0;
        $add->c_value               = isset($data['c_value']) ? $data['c_value'] : 0;
        $add->p_staff               = isset($data['p_staff']) ? $data['p_staff'] : 0;
        if ($data['p_staff'] == 1) {
            $add->delivery_charges_value = Admin::find(1)->costs_ship;
        }else {
            $add->delivery_charges_value = isset($data['delivery_charges_value']) ? $data['delivery_charges_value'] : null;
        }

        $add->delivery_min_distance = isset($data['delivery_min_distance']) ? $data['delivery_min_distance'] : 0;
        $add->delivery_min_charges_value    = isset($data['delivery_min_charges_value']) ? $data['delivery_min_charges_value'] : 0;
        $add->type_charges_value    = isset($data['type_charges_value']) ? $data['type_charges_value']: 1;
        $add->distance_max          = isset($data['distance_max']) ? $data['distance_max']: 0;
        
        $add->s_data                = serialize([$a,$b]);
        $add->service_del           = isset($data['service_del']) ? $data['service_del'] : 1;
        if(isset($data['img']))
        {
            $filename   = time().rand(111,699).'.' .$data['img']->getClientOriginalExtension(); 
            $data['img']->move("upload/user/", $filename);   
            $add->img = $filename;   
        }

        if(isset($data['password']))
        {
            $add->password      = bcrypt($data['password']);
            $add->shw_password  = $data['password'];
        }

         // Agregamos un arreglo con los dias laborales
         $stat_type = $type === 'add' ? 'add' : $type;
         $times = [];

         ($data['status_mon'] == 1) ? $mon = (isset($data['open_mon']) && isset($data['close_mon'])) ? $data['open_mon'].' - '.$data['close_mon'] : 'closed' : $mon = 'closed';
         ($data['status_tue'] == 1) ? $tue = (isset($data['open_tue']) && isset($data['close_tue'])) ? $data['open_tue'].' - '.$data['close_tue'] : 'closed' : $tue = 'closed';
         ($data['status_wed'] == 1) ? $wed = (isset($data['open_wed']) && isset($data['close_wed'])) ? $data['open_wed'].' - '.$data['close_wed'] : 'closed' : $wed = 'closed';
         ($data['status_thu'] == 1) ? $thu = (isset($data['open_thu']) && isset($data['close_thu'])) ? $data['open_thu'].' - '.$data['close_thu'] : 'closed' : $thu = 'closed';
         ($data['status_fri'] == 1) ? $fri = (isset($data['open_fri']) && isset($data['close_fri'])) ? $data['open_fri'].' - '.$data['close_fri'] : 'closed' : $fri = 'closed';
         ($data['status_sat'] == 1) ? $sat = (isset($data['open_sat']) && isset($data['close_sat'])) ? $data['open_sat'].' - '.$data['close_sat'] : 'closed' : $sat = 'closed';
         ($data['status_sun'] == 1) ? $sun = (isset($data['open_sun']) && isset($data['close_sun'])) ? $data['open_sun'].' - '.$data['close_sun'] : 'closed' : $sun = 'closed';
        

         array_push($times, [
             'mon' => $mon,
             'tue' => $tue,
             'wed' => $wed,
             'thu' => $thu,
             'fri' => $fri,
             'sat' => $sat,
             'sun' => $sun,
         ]);

        
        $add->save();

        //Add Times Week
        $op_times = new Opening_times;
        $op_times->addNew($times, $add->id);

        $gallery = new UserImage;
        $gallery->addNew($data,$add->id);
    }

    /*
    |--------------------------------------
    |Get all data from db
    |--------------------------------------
    */
    public function getAll()
    {
        return User::join('city','users.city_id','=','city.id')
                   ->leftjoin('categorystore','users.type','=','categorystore.id')
                   ->select('categorystore.name as Cat','users.*','city.name as city')
                   ->orderBy('users.id','DESC')->get();
    }

    public function getAppData($city_id,$trending = false)
    {
        $currency   = Admin::find(1)->currency;
        $lat        = isset($_GET['lat']) ? $_GET['lat'] : 0;
        $lon        = isset($_GET['lng']) ? $_GET['lng'] : 0;

        $res  = User::where(function($query) use($city_id,$trending){

            $query->where('status',0)->where('city_id',$city_id);

            if($trending)
            {
                $query->where('users.trending',1);
            }

            if(isset($_GET['banner']))
            {
                $sid   = BannerStore::where('banner_id',$_GET['banner'])->pluck('store_id')->toArray();

                $query->whereIn('users.id',$sid);
            }

            if(isset($_GET['q']))
            {
                $q   = $_GET['q'];
                $ids = Item::whereRaw('lower(name) like "%' . strtolower($q) . '%"')->pluck('store_id')->toArray();

                if(count($ids) > 0)
                {
                    $query->whereIn('users.id',$ids);
                }

                $query->whereRaw('lower(name) like "%' . strtolower($q) . '%"');
            }

        })->select('users.*',DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                * cos(radians(users.lat)) 
                * cos(radians(users.lng) - radians(" . $lon . ")) 
                + sin(radians(" .$lat. ")) 
                * sin(radians(users.lat))) AS distance"))
                ->orderBy('id','DESC')->skip(0)->take(5)->get();
        
        $data = [];

        foreach($res as $row)
        {
            
            
            $admin = Admin::find(1);

            /****** Function IsClose or IsOpen ******************/
            $op_time 	 = new Opening_times;

            if ($row->open == false) {
                $open 		 = ($op_time->ViewTime($row->id)['status'] != 0) ? true : false;
            }else {
                $open = false;
            }
            $time        = $op_time->ViewTime($row->id)['Time'];
            $opening_day = $op_time->ViewTime($row->id)['w_close'];
            /****** Function IsClose or IsOpen ******************/

          

            $totalRate    = Rate::where('store_id',$row->id)->count();
            $totalRateSum = Rate::where('store_id',$row->id)->sum('star');
            
            if($totalRate > 0)
            {
                $avg          = $totalRateSum / $totalRate;
            }
            else
            {
                $avg           = 0 ;
            }

            $data[] = [
                'times_check'   => $op_time->ViewTime($row->id),
                'id'            => $row->id,
                'title'         => $this->getLang($row->id,$_GET['lid'])['name'],
                'img'           => Asset('upload/user/'.$row->img),
                'address'       => $this->getLang($row->id,$_GET['lid'])['address'],
                'open'          => $open,
                'Status'        => $row->open,
                'Time'          => $time,
                'opening_day'   => $opening_day,
                'trending'      => $row->trending,
                'logo'          => $admin->logo ? Asset('upload/admin/'.$admin->logo) : null,
                'phone'         => $row->phone,
                'rating'        => $avg > 0 ? number_format($avg, 1) : '0.0',
                'images'        => $this->userImages($row->id),
                'ratings'       => $this->getRating($row->id),
                'person_cost'   => $row->person_cost,
                'cuenta_clave'  => $row->Cuenta_clave,
                'banco_name'    => $row->banco_name,
                'delivery_time' => $row->delivery_time,
                'type'          => CategoryStore::find($row->type)->name,
                'delivery_type' => $row->service_del,
                'currency'      => $currency,
                'discount'      => $this->discount($row->id,$currency)['msg'],
                'discount_value' => $this->discount($row->id,$currency)['value'],
                'items'          => $this->menuItem($row->id,$row->c_type,$row->c_value),
                'delivery_charges_value' => $this->SetCommShip($row->id,$row->p_staff,$row->distance_max,$row->distance),
                'c_value'       => $row->c_value,
                'c_type'        => $row->c_type,
                'max_distance'  => $this->GetMax_distance($row->distance_max,$row->distance),
                'distance'      => bcdiv($row->distance,'1',2),
                "distance_max"  => $row->distance_max,
                'km'            => number_format($row->distance,2)
            ];

        }
        
        return $data;
       
    }

    public function InTrending($city_id)
    {
        $currency   = Admin::find(1)->currency;
        $lat        = isset($_GET['lat']) ? $_GET['lat'] : 0;
        $lon        = isset($_GET['lng']) ? $_GET['lng'] : 0;
        $res  = User::where(function($query) use($city_id){

            $query->where('status',0)->where('city_id',$city_id);

            $query->where('users.trending',1);

        })->select('users.*',DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
        * cos(radians(users.lat)) 
        * cos(radians(users.lng) - radians(" . $lon . ")) 
        + sin(radians(" .$lat. ")) 
        * sin(radians(users.lat))) AS distance"))
        ->orderBy('id','DESC')->get();
        
        $data = [];

        foreach($res as $row)
        {
            $admin = Admin::find(1);

            /****** Function IsClose or IsOpen ******************/
            $op_time 	 = new Opening_times;

            if ($row->open == false) {
                $open 		 = ($op_time->ViewTime($row->id)['status'] != 0) ? true : false;
            }else {
                $open = false;
            }
            $time        = $op_time->ViewTime($row->id)['Time'];
            $opening_day = $op_time->ViewTime($row->id)['w_close'];
            /****** Function IsClose or IsOpen ******************/

          

            $totalRate    = Rate::where('store_id',$row->id)->count();
            $totalRateSum = Rate::where('store_id',$row->id)->sum('star');
            
            if($totalRate > 0)
            {
                $avg          = $totalRateSum / $totalRate;
            }
            else
            {
                $avg           = 0 ;
            }


            $data[] = [
                'id'            => $row->id,
                'title'         => $this->getLang($row->id,$_GET['lid'])['name'],
                'img'           => Asset('upload/user/'.$row->img),
                'address'       => $this->getLang($row->id,$_GET['lid'])['address'],
                'open'          => $open,
                'Status'        => $row->open,
                'Time'          => $time,
                'opening_day'   => $opening_day,

                'trending'      => $row->trending,
                'logo'          => $admin->logo ? Asset('upload/admin/'.$admin->logo) : null,
                'phone'         => $row->phone,
                'rating'        => $avg > 0 ? number_format($avg, 1) : '0.0',
                // 'open_time'     => date('h:i:A',strtotime($row->opening_time)),
                // 'close_time'    =>  date('h:i:A',strtotime($row->closing_time)),
                'images'        => $this->userImages($row->id),
                'ratings'       => $this->getRating($row->id),
                'person_cost'   => $row->person_cost,
                'cuenta_clave'  => $row->Cuenta_clave,
                'banco_name'    => $row->banco_name,
                'delivery_time' => $row->delivery_time,
                'type'          => CategoryStore::find($row->type)->name,
                'delivery_type' => $row->service_del,
                'currency'      => $currency,
                'discount'      => $this->discount($row->id,$currency)['msg'],
                'discount_value' => $this->discount($row->id,$currency)['value'],
                'items'          => $this->menuItem($row->id,$row->c_type,$row->c_value),
                'delivery_charges_value' => $this->SetCommShip($row->id,$row->p_staff,$row->distance_max,$row->distance),
                'c_value'       => $row->c_value,
                'c_type'        => $row->c_type,
                'max_distance'  => $this->GetMax_distance($row->distance_max,$row->distance),
                'distance'      => bcdiv($row->distance,'1',2),
                "distance_max"  => $row->distance_max,
                'km'            => number_format($row->distance,2)
            ];

        }
        
        return $data;
    }

    function SearchCat($city_id)
    {
        $currency   = Admin::find(1)->currency;
        $lat        = isset($_GET['lat']) ? $_GET['lat'] : 0;
        $lon        = isset($_GET['lng']) ? $_GET['lng'] : 0;
        $res  = User::where(function($query) use($city_id){

            $query->where('status',0)->where('city_id',$city_id);

            if (isset($_GET['cat'])) {
                $query->where('type',$_GET['cat']);
            }
            
        })->select('users.*',DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
        * cos(radians(users.lat)) 
        * cos(radians(users.lng) - radians(" . $lon . ")) 
        + sin(radians(" .$lat. ")) 
        * sin(radians(users.lat))) AS distance"))
        ->orderBy('id','DESC')->get();
        
        $data = [];

        foreach($res as $row)
        {
            $admin = Admin::find(1);

            /****** Function IsClose or IsOpen ******************/
            $op_time 	 = new Opening_times;

            if ($row->open == false) {
                $open 		 = ($op_time->ViewTime($row->id)['status'] != 0) ? true : false;
            }else {
                $open = false;
            }
            $time        = $op_time->ViewTime($row->id)['Time'];
            $opening_day = $op_time->ViewTime($row->id)['w_close'];
            /****** Function IsClose or IsOpen ******************/
            $totalRate    = Rate::where('store_id',$row->id)->count();
            $totalRateSum = Rate::where('store_id',$row->id)->sum('star');
            
            if($totalRate > 0)
            {
                $avg          = $totalRateSum / $totalRate;
            }
            else
            {
                $avg           = 0 ;
            }

            $data[] = [
                'id'            => $row->id,
                'title'         => $this->getLang($row->id,$_GET['lid'])['name'],
                'img'           => Asset('upload/user/'.$row->img),
                'address'       => $this->getLang($row->id,$_GET['lid'])['address'],
                'open'          => $open,
                'Status'        => $row->open,
                'Time'          => $time,
                'opening_day'   => $opening_day,
                'trending'      => $row->trending,
                'logo'          => $admin->logo ? Asset('upload/admin/'.$admin->logo) : null,
                'phone'         => $row->phone,
                'rating'        => $avg > 0 ? number_format($avg, 1) : '0.0',
                'images'        => $this->userImages($row->id),
                'ratings'       => $this->getRating($row->id),
                'person_cost'   => $row->person_cost,
                'cuenta_clave'  => $row->Cuenta_clave,
                'banco_name'    => $row->banco_name,
                'delivery_time' => $row->delivery_time,
                'type'          => CategoryStore::find($row->type)->name,
                'delivery_type' => $row->service_del,
                'currency'      => $currency,
                'discount'      => $this->discount($row->id,$currency)['msg'],
                'discount_value' => $this->discount($row->id,$currency)['value'],
                'items'          => $this->menuItem($row->id,$row->c_type,$row->c_value),
                'delivery_charges_value' => $this->SetCommShip($row->id,$row->p_staff,$row->distance_max,$row->distance),
                'c_value'       => $row->c_value,
                'c_type'        => $row->c_type,
                'max_distance'  => $this->GetMax_distance($row->distance_max,$row->distance),
                'distance'      => bcdiv($row->distance,'1',2),
                "distance_max"  => $row->distance_max,
                'km'            => number_format($row->distance,2)
            ];

        }
        
        return $data;
    }

    public function getTotsStores($city_id){
        $lat        = isset($_GET['lat']) ? $_GET['lat'] : 0;
        $lon        = isset($_GET['lng']) ? $_GET['lng'] : 0;

        $res  = User::where(function($query) use($city_id){
            $query->where('status',0)->where('city_id',$city_id);
        })->orderBy('id','DESC')->get();

        return $res->count();
    }

    public function GetAllStores($city_id)
    {
        $currency   = Admin::find(1)->currency;
        $lat        = isset($_GET['lat']) ? $_GET['lat'] : 0;
        $lon        = isset($_GET['lng']) ? $_GET['lng'] : 0;
        $init       = isset($_GET['init']) ? $_GET['init'] : 0;
        
        $res  = User::where(function($query) use($city_id){

            $query->where('status',0)->where('city_id',$city_id);

        })->select('users.*',DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                * cos(radians(users.lat)) 
                * cos(radians(users.lng) - radians(" . $lon . ")) 
                + sin(radians(" .$lat. ")) 
                * sin(radians(users.lat))) AS distance"))
                ->orderBy('id','DESC')->skip($init)->take(5)->get();
        
        
        $data = [];

        foreach($res as $row)
        {
            $admin = Admin::find(1);

            /****** Function IsClose or IsOpen ******************/
            $op_time 	 = new Opening_times;

            if ($row->open == false) {
                $open 		 = ($op_time->ViewTime($row->id)['status'] != 0) ? true : false;
            }else {
                $open = false;
            }
            $time        = $op_time->ViewTime($row->id)['Time'];
            $opening_day = $op_time->ViewTime($row->id)['w_close'];
            /****** Function IsClose or IsOpen ******************/

          

            $totalRate    = Rate::where('store_id',$row->id)->count();
            $totalRateSum = Rate::where('store_id',$row->id)->sum('star');
            
            if($totalRate > 0)
            {
                $avg          = $totalRateSum / $totalRate;
            }
            else
            {
                $avg           = 0 ;
            }


            $data[] = [
                
                'id'            => $row->id,
                'title'         => $this->getLang($row->id,$_GET['lid'])['name'],
                'img'           => Asset('upload/user/'.$row->img),
                'address'       => $this->getLang($row->id,$_GET['lid'])['address'],
                'open'          => $open,
                'Status'        => $row->open,
                'Time'          => $time,
                'opening_day'   => $opening_day,

                'trending'      => $row->trending,
                'logo'          => $admin->logo ? Asset('upload/admin/'.$admin->logo) : null,
                'phone'         => $row->phone,
                'rating'        => $avg > 0 ? number_format($avg, 1) : '0.0',
                // 'open_time'     => date('h:i:A',strtotime($row->opening_time)),
                // 'close_time'    =>  date('h:i:A',strtotime($row->closing_time)),
                'images'        => $this->userImages($row->id),
                'ratings'       => $this->getRating($row->id),
                'person_cost'   => $row->person_cost,
                'cuenta_clave'  => $row->Cuenta_clave,
                'banco_name'    => $row->banco_name,
                'delivery_time' => $row->delivery_time,
                'type'          => CategoryStore::find($row->type)->name,
                'delivery_type' => $row->service_del,
                'currency'      => $currency,
                'discount'      => $this->discount($row->id,$currency)['msg'],
                'discount_value' => $this->discount($row->id,$currency)['value'],
                'items'          => $this->menuItem($row->id,$row->c_type,$row->c_value),
                'delivery_charges_value' => $this->SetCommShip($row->id,$row->p_staff,$row->distance_max,$row->distance),
                'c_value'       => $row->c_value,
                'c_type'        => $row->c_type,
                'max_distance'  => $this->GetMax_distance($row->distance_max,$row->distance),
                'distance'      => bcdiv($row->distance,'1',2),
                "distance_max"  => $row->distance_max,
                'km'            => number_format($row->distance,2)
            ];

        }
        
        return $data;
    }

    public function SetCommShip($id,$type,$max_distance,$distance)
    {
        $req = null;
        $admin   = Admin::find(1);
       
        if ($type == 1) {
            // Los cobros son del admin
            if ($admin->c_type == 0) {
                // El cobro es en Base a KM
                $req = $this->Costs_shipKM(
                    $admin->c_value,
                    $admin->min_distance,
                    $admin->min_value,
                    $distance);
            }else {
                // El cobro es en Fijo
                $req = [
                    'costs_ship'    => $admin->c_value,
                    'duration'      => '0'
                ];
            }
        }
        else {
            // Los cobros son del usuarios
            $user   = User::find($id);
            if ($user->type_charges_value == 0) {
                // El cobro es en Base a KM
                $req = $this->Costs_shipKM(
                    $user->delivery_charges_value,
                    $user->delivery_min_distance,
                    $user->delivery_min_charges_value,
                    $distance);
            }else {
                // El cobro es en Fijo
                $req = [
                    'costs_ship'    => $user->delivery_charges_value,
                    'duration'      => '0'
                ];
            }
        }


        return $req;

    }

    public function getDeliveryType($id)
    {
        return User::select('service_del')->where('id',$id)->get();
    }

    public function getLang($id,$lid)
    {
        $lid  = $lid > 0 ? $lid : 0;
        $data = User::find($id);
        return [
            'name' => $data->name,
            'address' => $data->address,
            'time_delivery' => $data->delivery_time
        ];
    }

    public function discount($id,$currency)
    {
        $res =  OfferStore::join('offer','offer_store.offer_id','=','offer.id')
                         ->select('offer.*')
                         ->where('offer.status',0)
                         ->where('offer_store.store_id',$id)
                         ->orderBy('offer.id','DESC')
                         ->first();
        $msg = null;
        $val = 0;

        if(isset($res->id))
        {
            $val = $res->value;
            
            if($res->type == 0)
            {
                $msg = $res->value."% off use coupen ".$res->code;
            }
            else
            {
                $msg = $currency.$res->value." flat off use coupen ".$res->code;
            }
        }

        return ['msg' => $msg,'value' => $val];
    }

    public function getRating($id)
    {
        $res =  Rate::join('app_user','rate.user_id','=','app_user.id')
                   ->select('app_user.name as user','rate.*')
                   ->where('rate.store_id',$id)
                   ->orderBy('rate.id','DESC')
                   ->get();
        $data = [];

        foreach($res as $row)
        {
            $data[] = ['user' => $row->user,'star' => $row->star,'comment' => $row->comment,'date' => date('d-M-Y',strtotime($row->created_at))];
        }

        return $data;
    }

    public function userImages($id)
    {
        $res = UserImage::where('user_id',$id)->get();
        $data = [];
        
        foreach($res as $row)
        {
            $data[] = ['img' => Asset('upload/user/gallery/'.$row->img)];
        }

        return $data;
    }

    public function menuItem($id,$type,$value)
    {
        $data     = [];
        // where('status',0)->
        $cates    = Item::where('store_id',$id)->select('category_id')->distinct()->get();
        $sPrice   = 0;
        $mPrice   = 0;
        $lPrice   = 0;
        $NPrice   = 0;

        foreach($cates as $cate)
        {
            // where('status',0)->
            $items = Item::where('category_id',$cate->category_id)->where('store_id',$id)->orderBy('sort_no','ASC')->get();
            $count = [];

            foreach($items as $i)
            {
                $IPrice = $this->checaValor(intval(str_replace('$','',$i->small_price)));
                $MPrice = $this->checaValor(intval(str_replace('$','',$i->medium_price)));
                $LPrice = $this->checaValor(intval(str_replace('$','',$i->large_price)));

                // First
                if ($type == 0) {
                    // Es un valor fijo
                    if ($i->small_price) {
                        $IPrice = $this->checaValor(intval(str_replace('$','',$i->small_price)) + $value);
                        // $sPrice = $this->checaValor($IPrice + $value);
                    }
                    if ($i->medium_price) {
                        $MPrice = $this->checaValor(intval(str_replace('$','',$i->medium_price)) + $value);
                        // $mPrice = $this->checaValor($MPrice + $value);
                    }
                    if ($i->large_price) {
                        $LPrice = $this->checaValor(intval(str_replace('$','',$i->large_price)) + $value);
                        // $lPrice = $this->checaValor($LPrice + $value);
                    }
                }else {
                    if ($i->small_price) {
                        $IPrice = $this->checaValor((intval(str_replace('$','',$i->small_price)) * $value) / 100 + $i->small_price);
                        // $sPrice = $this->checaValor((($IPrice * $value) / 100 + $IPrice));
                    }
                    if ($i->medium_price) {
                        $MPrice = $this->checaValor((intval(str_replace('$','',$i->medium_price)) * $value) / 100 + $i->medium_price);
                        // $mPrice = $this->checaValor((($MPrice * $value) / 100 + $MPrice));
                    }
                    if ($i->large_price) {
                        $LPrice = $this->checaValor((intval(str_replace('$','',$i->large_price)) * $value) / 100 + $i->large_price);
                        // $lPrice = $this->checaValor((($LPrice * $value) / 100 + $LPrice));
                    }
                    
                }

                // First
                    if($i->small_price)
                    {
                        $price = $IPrice;
                    }
                    elseif(!$i->small_price && $i->medium_price)
                    {
                        $price = $MPrice;
                    }
                    elseif(!$i->small_price && !$i->medium_price)
                    {
                        $price = $LPrice;
                    }

                    if ($type == 0) {
                        // $NPrice = $this->checaValor($price + $value);
                    }else {
                        // $NPrice = $this->checaValor((($price * $value) / 100 + $price));
                    }

                // First
                    if($i->small_price)
                    {
                        $count[] = $IPrice;
                    }

                    if($i->medium_price)
                    {
                        $count[] = $MPrice;
                    }

                    if($i->large_price)
                    {
                        $count[] = $LPrice;
                    }
                
                
                // Items

                $item[] = [
                    'id'            => $i->id,
                    'name'          => $this->getLangItem($i->id,$_GET['lid'])['name'],
                    'img'           => $i->img ? Asset('upload/item/'.$i->img) : null,
                    'description'   => $this->getLangItem($i->id,$_GET['lid'])['desc'],
                    's_price'       => $IPrice,
                    'm_price'       => $MPrice,
                    'l_price'       => $LPrice,
                    'price'         => $price,
                    'count'         => count($count),
                    'nonveg'        => $i->nonveg,
                    'addon'         => $this->addon($i->id),
                    'status'        => $i->status
                ];

            }

            $data[] = [
                'id' => $cate->category_id,
                'sort_no' => $this->getLangCate($cate->category_id,
                $_GET['lid'])['sort_no'],
                'cate_name' => $this->getLangCate($cate->category_id,
                $_GET['lid'])['name'],
                'items' => $item
            ];

            unset($item);

        }

        return $data;
    }

    public function getLangCate($id,$lid)
    {
        $lid  = $lid > 0 ? $lid : 0;
        $data = Category::find($id);

        if($lid == 0)
        {
            if($data){
                return [
                'id'            => $data->id,
                'sort_no'       => $data->sort_no,
                'name'          => $data->name,
                'required'      => $data->required,
                'single_opcion' => $data->single_option,
                'max_options'   => $data->max_options
            ]   ;
            }
        }
        else
        {
            $data = unserialize($data->s_data);

            return ['name' => $data[$lid]];
        }
    }


    public function getLangItem($id,$lid)
    {
        $lid  = $lid > 0 ? $lid : 0;
        $data = Item::find($id);
        return ['name' => $data->name,'desc' => $data->description];
    }

    public function addon($id)
    {
        $i = 0;
        
        $item_addon  = ItemAddon::where('item_id',$id)->select('category_id')->distinct()->get();
        $data = [];
        // $items = [];
        $addon_items = [];
        $item = [];
        $pos = 0;
        
        foreach ($item_addon as $cate) {


            $addons = ItemAddon::where('category_id',$cate->category_id)->where('item_id',$id)->orderBy('category_id','ASC')->get();

            foreach ($addons as $add) {

                $addon = Addon::find($add->addon_id);
                if ($addon) {
                    $item[] = [
                        'id'            => $addon->id,
                        'name'          => $addon->name,
                        'price'         => $addon->price,
                    ];   
                }else {
                    ItemAddon::where('addon_id',$add->addon_id)->delete();
                }
                          
            }
            
            $data[] = [
                'cate_id'       => $this->getLangCate($cate->category_id,$_GET['lid'])['id'],
                'cate_sort_no'  => $this->getLangCate($cate->category_id,$_GET['lid'])['sort_no'],
                'cate_name'     => $this->getLangCate($cate->category_id,$_GET['lid'])['name'],
                'required'      => $this->getLangCate($cate->category_id,$_GET['lid'])['required'],
                'single_opcion' => $this->getLangCate($cate->category_id,$_GET['lid'])['single_opcion'],
                'max_options'   => $this->getLangCate($cate->category_id,$_GET['lid'])['max_options'],
                'items'         => $item
            ];
            
            unset($item);
        }

        
        return $data;
            
    }
    
  

    public function overview()
    {
        return [

        'order'     => Order::where('store_id',Auth::user()->id)->count(),
        'complete'  => Order::where('store_id',Auth::user()->id)->where('status',5)->count(),
        'month'     => Order::where('store_id',Auth::user()->id)->whereDate('created_at','LIKE',date('Y-m').'%')
                            ->count(),
        'items'     => Item::where('store_id',Auth::user()->id)->where('status',0)->count()

        ];
    }

    public function getSData($data,$id,$field)
    {
        $data = unserialize($data);

        return isset($data[$field][$id]) ? $data[$field][$id] : null;
    }

   public function login($data)
   {
     $chk = User::where('status',0)->where('email',$data['email'])->where('shw_password',$data['password'])->first();

     if(isset($chk->id))
     {
        return ['msg' => 'done','user_id' => $chk->id];
     }
     else
     {
        return ['msg' => 'Opps! Detalles de acceso incorrectos'];
     }
   }

   public function getCom($id,$total)
   {
     $order = Order::find($id);
     $user  = User::find($order->store_id);

     if($user->c_type == 0)
     {
        $val = $user->c_value;
     }
     else
     {
        $val = round($total * $user->c_value / 100);
     }

     return $val;
   }

    public function checaValor($numero)
    {
        $resultado = "";
        $numero = number_format($numero,2);
        $numeroSeparado = explode('.', $numero);
        $entero = $numeroSeparado[0];
        $decimales = $numeroSeparado[1];
        $decimales > 50 ? $resultado =  $entero+1 : $resultado = $entero; // .'.50'
        
        return $resultado;
    }

    public function GetMax_distance($max_distance,$distance)
    {
        if ($distance > $max_distance) 
        {
            $max = 0;
        }else {
            $max = 1;
        }
        
        return $max;
    }

    function Costs_shipKM($value,$min_distance,$min_value,$distance)
    {
        $km_inm       = $distance;

        if ($km_inm > 0) {
            if ($km_inm < $min_distance) {
                // la distancia es menor a la requerida
                $costs_ship  = intval($min_value);
            }else {
                $costs_ship = intval($value * $km_inm,2);
                $km_extra   = (round($km_inm) - $min_distance);
                $value_ext  = ($km_extra * $value);
                $costs_ship = ($min_value + $value_ext);
            }
        }else {
            $costs_ship = 0;
        }
        
        return [
            'costs_ship'    => $costs_ship,
            'duration'      => 0
        ];
    }

    
}
