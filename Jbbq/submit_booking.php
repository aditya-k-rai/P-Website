<?php
require __DIR__ . '/vendor/autoload.php';

function sendToGoogleSheets($data) {
    $client = new Google_Client();
    $client->setApplicationName('Jungle BBQ Bookings');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig('credentials.json');
    
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '19WYmbUIuF_9FdhYA93ZLrNGHj3RlDE56pWsQQX05Qv0';
    $range = 'Bookings!A:J'; // Assuming sheet name is "Bookings"
    
    // Format timestamp for India timezone
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date('Y-m-d H:i:s');
    
    // Structure data for sheets
    $values = [
        [
            $timestamp,              // Timestamp
            $data['name'],          // Name
            $data['phone'],         // Phone
            $data['email'],         // Email
            $data['date'],          // Date
            $data['time'],          // Time
            $data['persons'],       // Number of Guests
            $data['occasion'],      // Occasion
            $data['meal_preference'], // Meal Preference
            'Pending'               // Status
        ]
    ];
    
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    
    $params = [
        'valueInputOption' => 'RAW'
    ];
    
    try {
        $result = $service->spreadsheets_values->append(
            $spreadsheetId, 
            $range, 
            $body, 
            $params
        );
        
        return true;
    } catch (Exception $e) {
        error_log('Google Sheets Error: ' . $e->getMessage());
        return false;
    }
}

// Add validation function
function validateBookingData($data) {
    $errors = [];
    
    // Required field validation
    if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
        $errors[] = "Name is required and must be at least 3 characters";
    }
    
    if (empty($data['phone']) || !preg_match('/^[0-9]{10}$/', $data['phone'])) {
        $errors[] = "Valid 10-digit phone number is required";
    }
    
    if (empty($data['date'])) {
        $errors[] = "Date is required";
    }
    
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [
        'success' => false,
        'message' => 'An error occurred'
    ];
    
    $validationErrors = validateBookingData($_POST);
    
    if (!empty($validationErrors)) {
        $response['message'] = implode(", ", $validationErrors);
    } else if (sendToGoogleSheets($_POST)) {
        $response = [
            'success' => true,
            'message' => 'Your booking request has been submitted successfully!'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
