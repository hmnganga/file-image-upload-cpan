<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileType = $_FILES['file']['type'];
        $fileName = $_FILES['file']['name'];

        // Generate a unique identifier (timestamp or random string)
        $uniqueIdentifier = uniqid(); // You can use a timestamp or other method if you prefer.

        // Determine the target directory based on file type
        if (strpos($fileType, 'image/') === 0) {
            // Image file - save to 'imageuploads' folder
            $uploadDir = 'imageuploads/';
        } elseif ($fileType === 'application/pdf') {
            // PDF file - save to 'docuploads' folder
            $uploadDir = 'docuploads/';
        } else {
            // Invalid file type
            $response = [
                'status' => 'error',
                'message' => 'Invalid file type. Allowed types: JPG, PNG, PDF.',
            ];
            echo json_encode($response);
            exit;
        }

        // Get the file extension and create a new unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $uniqueIdentifier . '.' . $fileExtension;
        $filePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            // File upload successful
            $response = [
                'status' => 'ok',
                'message' => 'File uploaded successfully.',
                'url' => $filePath,
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'File upload failed.',
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'File upload error. Check the file and try again.',
        ];
    }

    echo json_encode($response);
} else {
    // Handle invalid requests
    http_response_code(405); // Method Not Allowed
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method. Use POST.',
    ];
    echo json_encode($response);
}
?>
