<?php
namespace Suroviy\Xcore;

use Eloquent;
use Input;

class PageField extends Eloquent
{	
    protected $table = 'page_fields';
    protected $fillable = ['page_id','key', 'value'];

    public function page()
    {
        return $this->belongsTo( 'App\Page', 'page_id', 'id' );
    }

}
