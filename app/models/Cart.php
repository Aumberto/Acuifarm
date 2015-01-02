<?php 

	Class Cart extends Eloquent {
      
       protected $table = 'carts';
       protected $fillable = array('member_id', 'pienso_id', 'amount', 'total');

       public function piensos(){

       	  return $this->belongsTo('Pienso', 'pienso_id');
       }

	}

 ?>