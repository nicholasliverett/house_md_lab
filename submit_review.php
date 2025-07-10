<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviews = get_reviews();
    $review = [
        'name' => $_POST['name'],
        'rating' => (int)$_POST['rating'],
        'comment' => $_POST['comment'],
        'date' => date('Y-m-d H:i:s')
    ];
    
    $reviews[] = $review;
    save_reviews($reviews);
}

header('Location: reviews.php');
exit;
?>