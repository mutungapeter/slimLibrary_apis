<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';



$config['displayErrorDetails'] = true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "trial";



$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/books', function (Request $request, Response $response) {
    $this->logger->Info("Book list");
    $mapper = new BookMapper($this->db);
    $books = $mapper->getBooks();

    //$response->getBody()->write(var_export($books, true));
    $response = $this->view->render($response, "books.phtml", ["books" => $books, "router" => $this->router]);
    return $response;
});

$app->get('/book/new', function (Request $request, Response $response) {
    $book_mapper = new BookMapper($this->db);
    $books= $book_mapper->getBooks();
    $response = $this->view->render($response, "bookadd.phtml", ["books" => $books]);
    return $response;
});
$app->post('/book/new', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $book_data = [];
    $book_data['book_name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $book_data['book_category'] = filter_var($data['category'], FILTER_SANITIZE_STRING);

    // work out the component
    
    $book = new BookEntity($book_data);
    $book_mapper = new bookMapper($this->db);
    $book_mapper->save($book);

    $response = $response->withRedirect("/books");
    return $response;
});

$app->get('/book/{id}', function (Request $request, Response $response, $args) {
    $book_id = (int)$args['id'];
    $mapper = new BookMapper($this->db);
    $book = $mapper->getBookById($book_id);

    $response = $this->view->render($response, "bookdetail.phtml", ["book" => $book]);
    return $response;
})->setName('book-detail');


$app->run();   
   