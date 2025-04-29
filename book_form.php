<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "book_db"); // Replace with your database credentials

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Validate Name
    if (empty($_POST['name'])) {
        $errors[] = "Name is required.";
    } elseif (!is_string($_POST['name']) || !preg_match('/^[a-zA-Z\s]+$/', $_POST['name'])) {
        $errors[] = "Name must contain only letters and spaces.";
    } else {
        $name = htmlspecialchars(trim($_POST['name']));
    }

    // Validate Email
    if (empty($_POST['email'])) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $email = htmlspecialchars(trim($_POST['email']));
    }

    // Validate Phone
    if (empty($_POST['phone'])) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
        $errors[] = "Phone number must be exactly 10 digits.";
    } else {
        $phone = htmlspecialchars(trim($_POST['phone']));
    }

    // Validate Address
    if (empty($_POST['address'])) {
        $errors[] = "Address is required.";
    } else {
        $address = htmlspecialchars(trim($_POST['address']));
    }


// Validate Location
    $valid_countries = [
        "United States", "Canada", "United Kingdom", "Australia", "India", 
        "Germany", "France", "Italy", "Spain", "China", "Japan", "Brazil","Thailand","Goa"
    // Add more country names as needed
    ];

    if (empty($_POST['location'])) {
        $errors[] = "Destination location is required.";
    } elseif (!in_array(trim($_POST['location']), $valid_countries)) {
        $errors[] = "Please enter a valid country name.";
    } else {
        $location = htmlspecialchars(trim($_POST['location']));
    }

    // Validate Guests
    if (empty($_POST['guests'])) {
        $errors[] = "Number of guests is required.";
    } elseif (!filter_var($_POST['guests'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        $errors[] = "Number of guests must be a positive integer.";
    } else {
        $guests = (int)$_POST['guests'];
    }

    // Validate Arrivals
    if (empty($_POST['arrivals'])) {
        $errors[] = "Arrival date is required.";
    } else {
        $arrivals = $_POST['arrivals'];
        $current_date = date("Y-m-d");
        if ($arrivals < $current_date) {
            $errors[] = "Arrival date must be today or later.";
        }
    }

    // Validate Leaving
    if (empty($_POST['leaving'])) {
        $errors[] = "Leaving date is required.";
    } elseif (!empty($arrivals) && $_POST['leaving'] <= $arrivals) {
        $errors[] = "Leaving date must be after the arrival date.";
    } else {
        $leaving = $_POST['leaving'];
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO book_form1 (name, email, phone, address, location, guests, arrivals, leaving) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiis", $name, $email, $phone, $address, $location, $guests, $arrivals, $leaving);

        if ($stmt->execute()) {
            // Redirect to book.php after successful submission
            header("Location: home.php");
            exit(); // Ensure no further code is executed after the redirect
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}

$conn->close();
?>