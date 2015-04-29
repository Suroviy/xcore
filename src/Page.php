<?php namespace Suroviy\Xcore;

use App\Menu;
use App\PageField;

use Eloquent;
use Validator;
use Input;

class Page extends Eloquent
{	
    static private $_constantModel = null;
    
    public $validator = null;
    
    protected $_field = null;


    public function menu()
    {
        return $this->belongsTo('App\Menu', 'menu_id','id');
    }
 
    public function pageField()
    {
    	return $this->hasMany('App\PageField', 'page_id','id');
    }
    
    public function getField($key,$default = null)
    {
        if (!$this->_field)
        {
            foreach ($this->pageField as $val)
            {
                $this->_field[$val->key] = $val->value;
            }
        }
        
        return (isset($this->_field[$key])) ? $this->_field[$key] : $default;
    }
    
    public function setField($fields)
    {
        $_fields = array();
        
        foreach ($this->pageField as $val)
        {
            
            $val->value = '1';
            $val->save();
            
            if (isset($fields[$val->key]))
            {
                $val->value = $fields[$val->key];
                $val->save();
                $_fields[$val->key]  = $val;
            }
        }
        
        foreach ($fields as $key => $val)
        {
            if (!isset($_fields[$key]))
            {
                PageField::create(['page_id'=>$this->id,'key'=>$key,'value'=>$val]);
            }
        }
    }
    
  
    public function save (array $option = array())
    {
        $id = ($this->id) ? ','.$this->id : null;
        
        $this->validator = Validator::make(
            array(
                'url' => $this->attributes['url'],
            ), array(
                'url' => 'unique:pages,url' . $id,
            ), array(
                'url.unique' => 'Такой url уже сущестует',
            )
        );

        if ($this->validator->fails())
        {
            return false;
        }
        return parent::save($option);
    }
    
    
    public function templateName ()
    {
        $template = Config('lara-cms.master.template');

        if (isset($template[$this->template]))
        {
            return $template[$this->template];
        }
        else
        {
            return null;
        }
    }
    
    
    static public function getConstantModel ()
    {
        return self::$_constantModel;
    }
    
    public function setConstantModel ($model = null)
    {
        if ($model)
        {
            self::$_constantModel = $model;
            Menu::setIdActiveMenu($model->menu_id);
        }
        else
        {
            self::$_constantModel = $this;
            Menu::setIdActiveMenu($this->menu_id);
        }
    }
    
    public function setUrlAttribute($value)
    {
        return $this->attributes['url'] = (empty($value)) ? null : $value;
    }
    
    public static function updateUrlParametr (array $newGet = array())
    {
        if (is_array(Input::all()))
        {
            $get = array_merge(Input::get(), $newGet);
            $array = array_where($get, function($key, $value) use ($newGet)
            {
                if (isset($newGet[$key]))
                {
                    return ($newGet[$key] !== false) ? true : false;
                }
                return true;
            });
            $param = ($param = http_build_query($array)) ? '?'.$param : null;
            return \URL::current().$param;
        }
        return \URL::current();
    }
    
    public function scopeGetParentFromMenu($query, $id = null)
    {
        $menu = ($id) ? Menu::find($id) : Menu::getActiveMenu(); 
        if (!$menu) return $query;
        $cildMenus = $menu->descendantsAndSelf()->get();
        $list = [];
        $ids = [$menu->id];
        $ids_not = ($menu->page_id) ? [$menu->page_id] : [];
        foreach ($cildMenus as $val) 
        {
            //echo $val->id.' ';
            $ids[] = $val->id;
            if ($val->page_id)
            {
                $ids_not[] = $val->page_id;
            }
        }

        if (sizeof($ids)>0)
        {
            $query->whereIn('menu_id',$ids)->whereNotIn('id',$ids_not);
        }
        
        return $query;
    }
}
