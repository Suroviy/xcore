<?php namespace Suroviy\Xcore;


use Eloquent;
use Validator;
use Input;

class Page extends Eloquent
{	
    static private $_constantModel = null;
    
    public $validator = null;

    public function menu()
    {
        return $this->belongsTo('App\Menu', 'menu_id','id');
    }
 
    public function pageField()
    {
    	return $this->hasMany('App\PageField', 'page_id','id');
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
        $template = Config('digital-code.master.template');

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
    
}
