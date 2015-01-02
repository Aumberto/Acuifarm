<?php 

	Class BookController extends BaseController{

		public function getIndex()
		{
			$books = Book::all();
			//$autor = $books->author;
            //echo $books;
            //echo $autor;
			return View::make('book.book_list')->with('books', $books);
		}
	}

 ?>