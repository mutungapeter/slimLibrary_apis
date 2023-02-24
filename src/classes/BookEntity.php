<?php

//This Entity class represents a domain object or entity, which is the  book. The Entity class is responsible for defining the structure and behavior of the entity, including its properties and methods. 
//The Entity classes would represent the model layer,
class BookEntity
{
    protected $id;
    protected $book_isbn;
    protected $book_name;
    protected $book_category;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }

        $this->book_isbn = $data['book_isbn'];
        $this->book_name = $data['book_name'];
        $this->book_category = $data['book_category'];
    }

    public function getId() {
        return $this->id;
    }

    public function getBook_name() {
        return $this->book_name;
    }

    public function getBook_isbn() {
       return $this->book_isbn;
    }

    public function getBook_category() {
        return $this->book_category;
    }
}
