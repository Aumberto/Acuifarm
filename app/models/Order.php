<?php 

   Class Order extends Eloquent{

      protected $table = 'orders';
      protected $fillable = array('member_id', 'address', 'total');

      public function orderItems()
      {
      	return $this->belongsToMany('Book') ->withPivot('amount', 'total');
      }

   }

 ?>