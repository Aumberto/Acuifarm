<?php 
	
	class FishtalkConsumo extends Eloquent{
        protected $connection = 'sqlsrv';
		protected $table = 'consumo';
		protected $fillable = array('Site', 'Cage', 'GroupID', 'Supplier', 'FeedType', 'ProductCode', 'FeedBatch', 'Amount', 'Date');

	}

 ?>