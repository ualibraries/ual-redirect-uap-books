<?php
require_once __DIR__ . "/dbconnect.php";

function get_new_data($db) {
    $query = 'SELECT A.guid, B.meta_value FROM wp_posts A JOIN wp_postmeta B ON A.ID = post_id WHERE post_type="book" AND meta_key="book_formats"';

    $ex = $db->prepare($query);
    $ex->execute();

    $bookdata = $ex->fetchAll();
    $reformatted_data = [];

    foreach ($bookdata as $book) {
        $reformatted_data[] = [
            "guid" => $book["guid"],
            "isbns" => get_isbns_from_meta_value($book)
        ];
    }
    return $reformatted_data;
}

function get_isbns_from_meta_value($book) {
    $isbns = [];
    $bookformats = json_decode($book["meta_value"]);
    foreach($bookformats as $format) {
        $isbns[] = $format;
    }

    return $isbns;
}

function get_old_data($db) {
    $query = 'SELECT A.active, A.id, B.isbn FROM book A JOIN isbnformatmap B ON A.id = B.bookid WHERE A.active = "T"';

    $ex = $db->prepare($query);
    $ex->execute();

    $data = $ex->fetchAll();
    $associated_book_data = [];

    foreach($data as $book_data) {
        $associated_book_data[$book_data["isbn"]] = $book_data["id"];
    }
    return $associated_book_data;

}

// Old UA Press Database
$dsn = "mysql:dbname=" . $db_name_old_press. ";host=" . $host;

try {
    $db = new PDO($dsn, $user_old_press, $pass_old_press);
} catch (PDOException $e) {
    echo "Connection falied: " . $e->getMessage();
    exit();
}

$old_data = get_old_data($db);

// New UA Press Database
$dsn = "mysql:dbname=" . $db_name_new_press. ";host=" . $host;

try {
    $db = new PDO($dsn, $user_new_press, $pass_new_press);
} catch (PDOException $e) {
    echo "Connection falied: " . $e->getMessage();
    exit();
}

$new_data = get_new_data($db);

$mapped_urls = [];

$old_url = "Books/bid";

foreach($new_data as $new_book_data) {
    foreach($new_book_data['isbns'] as $isbn) {
        if(isset($old_data[$isbn])) {
            $mapped_urls[$old_url . $old_data[$isbn] . ".htm"]['new_url']  = str_replace($local_wp_site_url, '', $new_book_data['guid']);
        }
        break;
    }
}

$file = 'mapped_uris.json';
$handle = fopen($file, 'w');
fwrite($handle, json_encode($mapped_urls));
fclose($handle);
