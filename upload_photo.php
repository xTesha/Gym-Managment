<?php

$photo = $_FILES['photo'];

$photo_name = basename($photo['name']);

$photo_path = 'member_photos/' . $photo_name;

# checking extensions of pictures
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

$ext = pathinfo($photo_name, PATHINFO_EXTENSION);

if(in_array($ext, $allowed_ext) && $photo['size'] < 20000000) {
    move_uploaded_file($photo['tmp_name'], $photo_path);
    # returning the message to js
    $response = ['success' => true, 'photo_path' => $photo_path];
    echo json_encode($response);
} else {
    $error_message = "Error: ";
    if (!in_array($ext, $allowed_ext)) {
        $error_message .= "Invalid file extension. ";
    }
    if ($photo['size'] >= 20000000) {
        $error_message .= "File size exceeds the limit. ";
    }
    $error_message .= "File not uploaded.";
    
    $response = ['success' => false, 'error' => $error_message];
    echo json_encode($response);
}
