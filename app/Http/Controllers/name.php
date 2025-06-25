<?php
echo "hellow world";
die;
// Database credentials
$servername = "localhost";  // MySQL server (usually 'localhost')
$username = "root"; // MySQL username
$password = "m8dV2x7yq9"; // MySQL password
$database = "bidinline_db"; // Database name

// Create a new connection to the database using mysqli
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate email
// $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
// if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     echo "Invalid or missing email.";
// } else {
//     // Check if email is unique in the `contractors` table
//     $emailQuery = "SELECT * FROM contractors WHERE email = ?";
//     $stmt = $conn->prepare($emailQuery);
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($result->num_rows > 0) {
//         echo "Email already exists.";
//         $stmt->close();
//         $conn->close();
//         exit;
//     }
//     $stmt->close();
// }

// Validate password and check confirmation
$password = $_POST['password'];

if (empty($password) || strlen($password) < 8) {
    echo "Password must be at least 8 characters.";
    $conn->close();
    exit;
} 

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Prepare the SQL query for inserting contractor data
$insertQuery = "
    INSERT INTO contractors 
    (company_name, tin, license_num, insurance_num, address, postal_code, city, country, state, representative_name, last_name, company_telephone, mobile_num, position, company_type, geographic_area, email, password, states, countries, identity_document)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

// Prepare the query
if ($stmt = $conn->prepare($insertQuery)) {
    // Convert arrays to JSON
    $company_type = json_encode($_POST['company_type']);
    $states = json_encode($_POST['states']);
    $countries = json_encode($_POST['countries']);
    $identity_document = ""; // Placeholder for identity document if needed

    // Bind the parameters correctly (22 placeholders matching 22 variables)
    $stmt->bind_param(
        "sssssssssssssssssssss",
        $_POST['company_name'],
        $_POST['tax_identification_number'],
        $_POST['license_number'],
        $_POST['insurance_number'],
        $_POST['address'],
        $_POST['postal_code'],
        $_POST['city'],
        $_POST['country'],
        $_POST['state'],
        $_POST['representative_name'],
        $_POST['last_name'],
        $_POST['company_telephone'],
        $_POST['mobile_telephone'],
        $_POST['position'],
        $company_type,
        $_POST['geo_graphical_area'],
        $_POST['email'],
        $hashedPassword,
        $states,
        $countries,
        $identity_document
    );

    // Execute the query
    if ($stmt->execute()) {
        echo "New contractor created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error in preparing SQL statement.";
}

// Close the database connection
$conn->close();
?>
