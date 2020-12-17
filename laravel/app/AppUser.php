<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Mail;
class AppUser extends Authenticatable
{
   protected $table = 'app_user';

   public function addNew($data)
   {
     $count = AppUser::where('email',$data['email'])->count();

     if($count == 0)
     {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $add                = new AppUser;
            $add->name          = $data['name'];
            $add->email         = $data['email'];
            $add->phone         = isset($data['phone']) ? $data['phone'] : 'null';
            $add->password      = $data['password'];
            $add->pswfacebook   = isset($data['pswfb']) ? $data['pswfb'] : 0;
            $add->save();

            return ['msg' => 'done','user_id' => $add->id];
        }else {
            return ['msg' => 'Opps! El Formato del Email es invalido'];
        }
    
     }
     else
     {
        return ['msg' => 'Opps! Este correo electrónico ya existe.'];
     }
   }

   public function SignPhone($data) 
   {
        $res = AppUser::where('id',$data['user_id'])->first();

        if(isset($res->id))
        {
            $res->phone = $data['phone'];
            $res->save();

            $return = ['msg' => 'done','user_id' => $res->id];
        }
        else
        {
            $return = ['msg' => 'error','error' => '¡Lo siento! Algo salió mal.'];
        }

        return $return;
   }

   public function login($data)
   {
     $chk = AppUser::where('email',$data['email'])->where('password',$data['password'])->first();

     if(isset($chk->id))
     {
        return ['msg' => 'done','user_id' => $chk->id];
     }
     else
     {
        return ['msg' => 'Opps! Detalles de acceso incorrectos'];
     }
   }

   public function Newlogin($data) 
   {
    $chk = AppUser::where('phone',$data['phone'])->first();

    if(isset($chk->id))
    {
       return ['msg' => 'done','user_id' => $chk->id];
    }
    else
    {
       return ['msg' => 'Opps! El usuario no existe...'];
    }
   }

   public function loginfb($data) 
   {
    $chk = AppUser::where('email',$data['email'])->where('pswfacebook',$data['password'])->first();

    if(isset($chk->id))
    {
       return ['msg' => 'done','user_id' => $chk->id];
    }
    else
    {
       return ['msg' => 'Opps! Detalles de acceso incorrectos'];
    }
   }

   public function updateInfo($data,$id)
   {
      $count = AppUser::where('id','!=',$id)->where('email',$data['email'])->count();

     if($count == 0)
     {
        $add                = AppUser::find($id);
        $add->name          = $data['name'];
        $add->email         = $data['email'];
        $add->phone         = $data['phone'];
        
        if(isset($data['password']))
        {
          $add->password    = $data['password'];
        }

        $add->save();

        return ['msg' => 'done','user_id' => $add->id,'data' => $add];
     }
     else
     {
        return ['msg' => 'Opps! Este correo electrónico ya existe.'];
     }
   }

    public function forgot($data)
    {
        $res = AppUser::where('email',$data['email'])->first();

        if(isset($res->id))
        {
            $otp = rand(1111,9999);

            $res->otp = $otp;
            $res->save();

            $para       =   $data['email'];
            $asunto     =   'Codigo de acceso - Deliverys app';
            $mensaje    =   "Hola ".$res->name." Un gusto saludarte, se ha pedido un codigo de recuperacion para acceder a tu cuenta en Delivery's app";
            $mensaje    .=  ' '.'<br>';
            $mensaje    .=  "Tu codigo es: <br />";
            $mensaje    .=  '# '.$otp;
            $mensaje    .=  "<br /><hr />Recuerda, si no lo has solicitado tu has caso omiso a este mensaje y te recomendamos hacer un cambio en tu contrasena.";
            $mensaje    .=  "<br/ ><br /><br /> Te saluda el equipo de  Delivery's app";
        
            $cabeceras = 'From: soporte.deliverysapp@gmail.com' . "\r\n";
            
            $cabeceras .= 'MIME-Version: 1.0' . "\r\n";
            
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            mail($para, $asunto, utf8_encode($mensaje), $cabeceras);
        
            //    Mail::send('email',['res' => $res], function($message) use($res)
        //    {     
        //       $message->to($res->email)->subject("Password Reset");
                    
        // YiYo Garcia
        // 614 131 9402
        // societacreativa@hotmail.com
        // mercadotecnia@pavosparson.com

        //     1. tengo 50 usuarios que han descargado la app 
        //     2. la inversion inicial es de $150 pesos por sucursal para inscrbirte en nuestra apliacion 
        //     3. Nosotros Empresa le instalaremos tanto a las sucursulas como a los repartidores la aplicacion para la funcionalidad y vente de sus productos de manera gratuita
        //     4. Dentro de la aplicacion aparecera su logo, sus direcciones, sus telefonos, y una lista de productos con precio y fotografia (misma que me tiene que proporcionar pavos parson)
        //     5. Por cada venta que se haga a travez de la app pavos parson pagara $7.00 pesos, mismos que se cobraran de manera acumulatoria cada 15 dias.
        //     6. tanto el costo de la inscrpicion como el total del cobro de los 7 pesos por cada venta o reparto a domcilio sera faturado a pavos parson, por <razon social> RFC Direccion, Email, Telefono. 

        // especificar cada link

        // Somos una empresa 100% chihuahuense creada por desarrolladores de nuevo casas grandes, comprometidos con el desarrollo de nuestra region.

        //     });

        $return = ['msg' => 'done','user_id' => $res->id];
        }
        else
        {
            $return = ['msg' => 'error','error' => '¡Lo siento! Este correo electrónico no está registrado con nosotros.'];
        }

        return $return;
    }

    public function verify($data)
    {
        $res = AppUser::where('id',$data['user_id'])->where('otp',$data['otp'])->first();

        if(isset($res->id))
        {
            $return = ['msg' => 'done','user_id' => $res->id];
        }
        else
        {
            $return = ['msg' => 'error','error' => '¡Lo siento! OTP no coincide.'];
        }

        return $return;
    }

    public function updatePassword($data)
    {
        $res = AppUser::where('id',$data['user_id'])->first();

        if(isset($res->id))
        {
            $res->password = $data['password'];
            $res->save();

            $return = ['msg' => 'done','user_id' => $res->id];
        }
        else
        {
            $return = ['msg' => 'error','error' => '¡Lo siento! Algo salió mal.'];
        }

        return $return;
    }

    public function countOrder($id)
    {
        return Order::where('user_id',$id)->where('status','>',0)->count();
    }
}
