<?php
// This block should be saved in a separate file named "upload.php"

header('Content-Type: application/json'); // Set header for JSON response

$target_dir = "uploads/"; 
$response = [];
$uploadOk = 1;
$message = "";

if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0775, true)) {
            $message = "Error: Failed to create the upload directory.";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 1 && !is_writable($target_dir)) {
        $message = "Error: The directory '$target_dir' is not writable. Check permissions.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        $file_basename = basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $file_basename;
        $file_name = htmlspecialchars($file_basename);

        if (file_exists($target_file)) {
            $message = "Error: A file with the name ". $file_name . " already exists.";
            $uploadOk = 0;
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $message = "The file ". $file_name . " has been uploaded successfully.";
            } else {
                $message = "Sorry, there was an error moving the uploaded file.";
                $uploadOk = 0;
            }
        }
    }
} else {
    $errorCode = isset($_FILES["fileToUpload"]["error"]) ? $_FILES["fileToUpload"]["error"] : 4;
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE: $message = "Error: File is larger than server allows (php.ini)."; break;
        case UPLOAD_ERR_FORM_SIZE: $message = "Error: File is larger than form allows."; break;
        case UPLOAD_ERR_PARTIAL: $message = "Error: The file was only partially uploaded."; break;
        case UPLOAD_ERR_NO_FILE: $message = "Error: No file was uploaded."; break;
        default: $message = "An unknown upload error occurred."; break;
    }
    $uploadOk = 0;
}

// Prepare the JSON response
$response['status'] = $uploadOk ? 'success' : 'error';
$response['message'] = $message;

// Send the JSON response
echo json_encode($response);
exit();
?>