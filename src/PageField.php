<?php
namespace Suroviy\Xcore;

use Eloquent;
use Input;

class PageField extends Eloquent
{	

    public function page()
    {
        return $this->belongsTo( 'App\Page', 'page_id', 'id' );
    }

}
