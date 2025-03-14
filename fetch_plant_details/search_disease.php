<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "botanical_eye"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Search Results</title>
    <style>
        /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background Image Styling */
        body {
            background: url('scene.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
        }

        /* Navbar Styles */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 5%;
    background: linear-gradient(135deg, #4caf50, #2e7d32);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

/* Spacing to prevent overlap with content */
body {
    padding-top: 100px; /* Ensures content starts below navbar */
}

/* Logo */
.logo {
    font-size: 22px;
    font-weight: bold;
}

/* Navigation Links */
.nav-links {
    list-style: none;
    display: flex;
}

.nav-links li {
    margin: 0 15px;
}

.nav-links a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    transition: 0.3s;
}

.nav-links a:hover {
    color: #c8e6c9;
}

/* Logout Button */
.logout-btn {
    background: red;
    padding: 8px 12px;
    border-radius: 5px;
    font-weight: bold;
}

.logout-btn:hover {
    background: darkred;
}
        

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.9);
            color: black;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        tr:hover {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">Botanical Eye</div>
        <ul class="nav-links">
            <li><a href="http://localhost:8080/home/home.html">Home</a></li>
            <li><a href="http://127.0.0.1:5000/">Prediction</a></li>
            <li><a href="http://127.0.0.1:5000/disease-prediction">Plot Graph</a></li>
            <li><a href="http://localhost:8080/fetch_plant_details/search_disease.html">Plant Info</a></li>
            <li><a href="http://localhost:8080/medicine_store/">Plant Medicine Store</a></li>
            <li><a href="http://localhost:8080/login_signup_page/index.html" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Disease Search Results</h2>

        <?php
        // Check if a disease name is provided
        if (isset($_GET['disease_name']) && !empty($_GET['disease_name'])) {
            $disease_name = $_GET['disease_name'];

            // Query to search disease details
            $sql = "SELECT * FROM diseases WHERE common_name LIKE ? OR scientific_name LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchParam = "%$disease_name%";
            $stmt->bind_param("ss", $searchParam, $searchParam);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display results
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr>";
                echo "<th>Common Name</th>";
                echo "<th>Scientific Name</th>";
                echo "<th>Family</th>";
                echo "<th>Origin</th>";
                echo "<th>Description</th>";
                echo "<th>Habitat</th>";
                echo "<th>Soil Type</th>";
                echo "<th>Medicinal Uses</th>";
                echo "<th>Industrial Uses</th>";
                echo "<th>Maintenance</th>";
                echo "<th>Pest Management</th>";
                echo "<th>Toxicity</th>";
                echo "<th>Conservation Status</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['common_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['scientific_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['family']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['habitat_distribution']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['soil']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['medicinal_uses']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['industrial_uses']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['maintenance']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pest_management']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['toxicity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['conservation_status']) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p style='color:red;'>No disease found with the name '$disease_name'.</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>Please enter a disease name.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>
