<?php
require_once __DIR__ . "/dbconnect.php";

// Old UA Press Database
$dsn = "mysql:dbname=" . $db_name_old_press. ";host=" . $host;

try {
    $db = new PDO($dsn, $user_old_press, $pass_old_press);
} catch (PDOException $e) {
    echo "Connection falied: " . $e->getMessage();
    exit();
}

$query = 'SELECT A.active, A.id, B.isbn FROM book A JOIN isbnformatmap B ON A.id = B.bookid WHERE A.active = "T"';

$ex = $db->prepare($query);
$ex->execute();

$data_old_press = $ex->fetchAll();

// New UA Press Database
$dsn = "mysql:dbname=" . $db_name_new_press. ";host=" . $host;

try {
    $db = new PDO($dsn, $user_new_press, $pass_new_press);
} catch (PDOException $e) {
    echo "Connection falied: " . $e->getMessage();
    exit();
}

$query = 'SELECT A.guid, B.meta_value FROM wp_posts A JOIN wp_postmeta B ON A.ID = post_id WHERE post_type="book" AND meta_key="book_isbn"';

$ex = $db->prepare($query);
$ex->execute();

$data_new_press = $ex->fetchAll();

$data_new_press_by_isbn = [];
$mapped_urls = [];

$old_url = "Books/bid";

foreach ($data_new_press as $book) {
    $data_new_press_by_isbn[$book['meta_value']]['url'] = $book['guid'];
}

foreach ($data_old_press as $old_press_book) {
    if (isset($data_new_press_by_isbn[$old_press_book['isbn']])) {
        $mapped_urls[$old_url . $old_press_book['id'] . '.htm']['new_url'] = str_replace($local_wp_site_url, '', $data_new_press_by_isbn[$old_press_book['isbn']]['url']);
    }
}

$file = 'mapped_uris.json';
$handle = fopen($file, 'w');
fwrite($handle, json_encode($mapped_urls));
fclose($handle);
