<?php

//The Mapper class is responsible for persisting Entity objects to and retrieving them from a data store, such as a database or a file system.
// The Mapper class is responsible for translating between the domain-specific Entity objects and the persistence-specific representation of those objects. 
//For example, This BookMapper class might be responsible for mapping Book objects to and from rows in a database table.


class BookMapper extends Mapper
{
    public function getBooks() {
        $sql = "SELECT t.id,  t.book_isbn, t.book_name, t.book_category
            from books t
        ";
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new BookEntity($row);
        }
        return $results;
    }

    /**
     * Get one book by its ID
     *
     * @param int $book_id The ID of the book
     * @return BookEntity  The book
     */
    public function getBookById($book_id) {
        $sql = "SELECT t.id, t.book_name, t.book_isbn, book_category
            FROM books t
            WHERE t.id =:book_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["book_id" => $book_id]);

        if($result) {
            return new BookEntity($stmt->fetch());
        }

    }

    public function save(BookEntity $book) {
        $sql = "INSERT into books
            (book_isbn, book_name, book_category) VALUES
            (:book_isbn, :book_name, :book_category)";
        $stmt = $this->db->prepare($sql);

        
        $result = $stmt->execute([
            "book_isbn" => $book->getBook_isbn(),
            "book_name" => $book->getBook_name(),
            "book_category" => $book->getBook_category(),
            
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
    }
}

?>