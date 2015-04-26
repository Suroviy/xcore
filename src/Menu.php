<?php

namespace Suroviy\Xcore;

use Eloquent;
use Cache;
use DB;

use Baum\Node;

class Menu extends Node
{
    protected $appends = [
        'menu_active',
        'make_url',
        'make_title'
    ];
    
    protected $casts = [
        'menu_active' => 'boolean',
    ];
    
    protected $guarded = array('id', 'lidx', 'ridx', 'depth');
    
    protected $hidden = ['page'];
    
    public static $lifeCache = 10;
    
    protected static $_activeMenu = null;


    protected static $_idActiveMenu = null;
    protected static $_mapIdParent = null;
    protected static $_mapPrentId = null;
    
    
    public static function setActiveMenu ( $model )
    {
        self::$_activeMenu = $model;
    }
    /**
     * Получить ID активного пунткта меню
     * @return type
     */
    public static function getActiveMenu ()
    {
        return self::$_activeMenu;
    }
    
    /**
     * назначает ID активного пункта меню
     * как правило ID активного пункта получается при инициализации страници
     * @param type $id
     */
    public static function setIdActiveMenu ( $id )
    {
        self::$_idActiveMenu = $id;
        self::$_activeMenu = Menu::find($id);
    }
    /**
     * Получить ID активного пунткта меню
     * @return type
     */
    public static function getIdActiveMenu ()
    {
        return self::$_idActiveMenu;
    }

    public function setParentAttribute($value)
    {
        return $this->attributes['parent'] = ($value == null) ? 0 : $value;
    }
    
    public function makeTitle()
    {
        if (!empty($this->title))
        {
             return $this->title;
        }
        elseif($this->page)
        {
             return $this->page->title;
        }
        else
        {
            return null;
        }
    }
    
    public function makeUrl()
    {
        if (!empty($this->url))
        {
             return ($this->url == '/') ? $this->url :'/'.$this->url;
        }
        elseif($this->page)
        {
             return ($this->page->url == '/') ? $this->page->url : '/'.$this->page->url;
        }
        else
        {
            return null;
        }
    }
    /**
     * объект link может использоватся для связования данных с моделью
     * тоесть при использование функции makeTitle() данные будут братся из привязаной модели по линку
     * если такой модели нет то данные будт взяты локально
     * @return type
     */
    
    public function page()
    {
        return $this->belongsTo('App\Page');
    }
    
    public function parent()
    {
        return $this->belongsTo('App\Menu', 'parent_id', 'id')->with('page');
    }

    public function childs()
    {
        return $this->hasMany( 'App\Menu', 'parent_id', 'id' )->with('page')->orderBy('lft','asc');
    }
    
    /**
     * создание мапы array('id'=>'parent_id')
     * @return type
     */
    static public function getMapIdParent()
    {
        if (self::$_mapIdParent)
        {
            return self::$_mapIdParent;
        }

        $model = Cache::remember('map_tree', self::$lifeCache, function()
        {
            return DB::table('menus')->get();
        });

        $map = array();

        foreach ($model as $val)
        {
            $map[$val->id] = $val->parent_id;
        }
        unset($val);
        
        self::$_mapIdParent = $map;
       
        return $map;
    }
    
    /**
     * создание мапы array('parent_id'=>'id')
     * @return type
     */
    static public function getMapParentId()
    {
        if (self::$_mapPrentId)
        {
            return self::$_mapPrentId;
        }
        
        $model = Cache::remember('map_tree', self::$lifeCache, function()
        {
            return DB::table('menus')->get();
        });
        
        $map = array();
        foreach ($model as $val)
        {
            $map[$val->parent_id][] = $val->id;
        }
        unset($val);
        
        self::$_mapPrentId = $map;
        return $map;
    } 
    
    /**
     * получение ID всех активных предков
     * @param type $id
     * @return type
     */
    static public function getIdParents($id = null)
    {
        $id = ($id) ? $id : self::getIdActiveMenu();
        
        $idParent = self::getMapIdParent();
        
        if (!is_array( $id ))
        {
            if (isset( $idParent[ $id ] ))
            {
                $id = array($id);
            }
            else
            {
                return array();
            }
        }
        
        if (isset( $idParent[ end( $id ) ] ))
        {
            if (isset($idParent[ $idParent[ end( $id ) ] ]))
            {
                $id[] = $idParent[ end( $id ) ];
                return self::getIdParents($id);
            }
        }        
        
        return array_reverse( $id );       
    }
    
    /**
     * получение ID всех потомков со всех уровней
     * @param type $id
     * @return type
     */
    static public function getAllIdChilds($id)
    {
        $map = self::getMapParentId();
        
        $childId = array();
        
        if (isset( $map[$id] ))
        {
            foreach ($map[$id] as $val)
            {
                $childId = array_merge($childId,self::getAllIdChilds($val));
            }
            unset($val);
            
            return $childId;
        }
        else
        {
            return array( $id );
        }              
    }
    /**
     * опридилть активенди данный экземпляр класса тоесть
     * если ID класса присуствует в активных предках или сам является активным пунктом меню
     * @return boolean
     */
    public function menuActive()
    {
        $idParents = self::getIdParents();
        
        if (in_array( $this->id, $idParents ))
        {
            return true;
        }
        return false;
    }
    
    public function getMenuActiveAttribute($value)
    {
        return $this->menuActive();
    }
    
    public function getMakeUrlAttribute($value)
    {
        return $this->makeUrl();
    }
    
    public function getMakeTitleAttribute($value)
    {
        return $this->makeTitle();;
    }

}
