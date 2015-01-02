<?php 

   Class Author extends Eloquent {

   	  protected $table = 'authors';
   	  protected $fillable = array('name', 'surname');

   	  public function books()
   	  {
   	  	return $this->hasMany('Book', 'author_id');
   	  }
   }

 ?>