<?php 

	Class Book extends Eloquent {
    
		protected $table = 'books';
		protected $fillable = array('title', 'isbn', 'cover', 'price', 'author_id');

		public function author(){
			return $this->belongsTo('Author');
		}
	}

 ?>