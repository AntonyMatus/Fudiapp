<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Auth;
class CategoryStore extends Authenticatable
{
    protected $table = "categorystore";
    /*
    |----------------------------------------------------------------
    |   Validation Rules and Validate data for add & Update Records
    |----------------------------------------------------------------
    */
    
    public function rules($type)
    {
        return [

            'name'      => 'required',

            ];
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
        $a                  = isset($data['lid']) ? array_combine($data['lid'], $data['l_name']) : [];
        $add                = $type === 'add' ? new CategoryStore : CategoryStore::find($type);
        $add->name          = isset($data['name']) ? $data['name'] : null;
        $add->status        = isset($data['status']) ? $data['status'] : null;
        $add->sort_no       = isset($data['sort_no']) ? $data['sort_no'] : 0;
        if(isset($data['img']))
        {
            $filename   = time().rand(111,699).'.' .$data['img']->getClientOriginalExtension(); 
            $data['img']->move("upload/categorys/", $filename);   
            $add->img = $filename;   
        }

        $add->s_data        = serialize($a);
        $add->save();
    }

    /*
    |--------------------------------------
    |Get all data from db
    |--------------------------------------
    */
    public function getAll()
    {
        return CategoryStore::orderBy('sort_no','ASC')->get();
    }

    public function getAllCats()
    {
        $res  = CategoryStore::orderBy('sort_no','ASC')->get();
        $data = [];

        foreach($res as $row)
        {
            
            $data[] = [
            'id'            => $row->id,
            'name'          => $row->name,
            'img'           => $row->img ? Asset('upload/categorys/'.$row->img) : null,
            'status'        => $row->status,
            'sort_no'       => $row->sort_no,
            ];

        }
        
        return $data;
    }

    public function getSData($data,$id,$field)
    {
        $data = unserialize($data);

        return isset($data[$id]) ? $data[$id] : null;
    }
}
