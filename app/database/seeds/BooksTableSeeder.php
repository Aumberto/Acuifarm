<?php 
   
   class BooksTableSeeder extends Seeder {

      public function run()
      {
      	DB::table('books')->delete();

      	Book::create(array(
      					'title' => 'Requiem',
      					'isbn' => '9780062014535',
                     'price' => '13.40',
                     'cover' => 'requiem.jpg',
                     'author_id' => 1
      		));

      	Book::create(array(
                     'title' => 'Libro 2',
                     'isbn' => '97823464014535',
                     'price' => '23.40',
                     'cover' => 'libro2.jpg',
                     'author_id' => 2
            ));

      	Book::create(array(
                     'title' => 'Uno cualquiera',
                     'isbn' => '97800345672535',
                     'price' => '53.40',
                     'cover' => 'unocualquiera.jpg',
                     'author_id' => 3
            ));
      }

   }

 ?>
