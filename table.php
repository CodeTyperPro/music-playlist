<?php
session_start();

$search = $_GET['search'] ?? '';

function formatArrayToString($array)
{
    $separator = " | ";
    return implode($separator, $array);
}

function isMatched($data, $search)
{
    if (strcmp($search, '') === 0) {
        return true;
    }

    foreach ($data as $field) {
        if (strpos($field, $search) !== false) {
            return true;
        }
    }

    return false;
}

include_once('storage.php');
//$stor = new Storage(new JsonIO('data/users.json'));
//$user = $stor->findById($_SESSION['user_id']);
$stor = new Storage(new JsonIO('data/tracks.json'));
$tracks = $stor->findAll();
$filtered_data = [];
foreach ($tracks as $d) {
    $fields = [
        $d['title'],
        $d['artist'],
        $d['length'],
        $d['year'],
        formatArrayToString($d['genres']),
    ];
    if (isMatched($fields, $search)) {
        $filtered_data[] = [
            'title'  => $d['title'],
            'artist' => $d['artist'],
            'length' => $d['length'],
            'year'   => $d['year'],
            'genres' => formatArrayToString($d['genres']),
            'id'     => $d['id']
        ];
    }
}

usort($filtered_data, fn($a, $b) => strcmp(strtoupper($a['title']), strtoupper($b['title'])));
header('Content-Type: application/json');
echo json_encode($filtered_data, JSON_PRETTY_PRINT);

?>
