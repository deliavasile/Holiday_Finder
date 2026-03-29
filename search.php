<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dreamspot";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$destination   = $_POST['destination'];
$departure     = $_POST['departure-date'];
$return        = $_POST['return-date'];
$people        = isset($_POST['people']) ? (int)$_POST['people'] : 1;
$accommodation = $_POST['accommodation'];
$flight_class  = $_POST['flight-class'];

$minPrice = isset($_POST['min-price']) && $_POST['min-price'] !== '' ? (int)$_POST['min-price'] : 0;
$maxPrice = isset($_POST['max-price']) && $_POST['max-price'] !== '' ? (int)$_POST['max-price'] : 10000;

$sql = "SELECT * FROM holidays 
        WHERE destination LIKE ? 
        AND departure_date = ? 
        AND return_date = ? 
        AND accommodation_type = ? 
        AND flight_class = ?
        AND price BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$like_dest = "%" . $destination . "%";
$stmt->bind_param("sssssii", $like_dest, $departure, $return, $accommodation, $flight_class, $minPrice, $maxPrice);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>DreamSpot Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff;
      color: #333;
    }

    header {
      background: #004080;
      color: white;
      padding: 1.2rem 0.5rem;
      text-align: center;
      font-weight: 600;
      font-size: 1.3rem;
      letter-spacing: 0.8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    header h1 {
      font-size: 2.2rem;
      margin: 0.3rem;
    }

    header p {
      font-size: 1rem;
      margin: 0;
      font-weight: 400;
      opacity: 0.95;
    }

    main {
      max-width: 900px;
      margin: 3rem auto;
      padding: 1rem 2rem;
    }

    .result-box {
      margin-bottom: 2rem;
      padding: 1.5rem;
      border: 1px solid #ccddee;
      border-radius: 12px;
      background: #f9fcff;
      box-shadow: 0 2px 10px rgba(0, 64, 128, 0.06);
      overflow: auto;
    }

    .result-box img {
      float: right;
      width: 300px;
      border-radius: 10px;
      margin-left: 20px;
      margin-bottom: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h3 {
      margin-top: 0;
      color: #004080;
    }

    p {
      margin: 0.5rem 0;
    }

    button {
      margin-top: 1rem;
      background: #004080;
      color: white;
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0, 64, 128, 0.2);
      transition: background 0.3s ease;
    }

    button:hover {
      background: #002b55;
    }

    .no-results {
      text-align: center;
      font-size: 1.1rem;
      color: #555;
      margin-top: 4rem;
    }

    footer {
      background: #222;
      color: white;
      text-align: center;
      padding: 1rem;
      font-size: 0.9rem;
      margin-top: 3rem;
    }
  </style>
</head>
<script>
function confirmBooking(destination) {
  alert("Booking confirmed for " + destination + "!");
  setTimeout(() => {
    window.location.href = "plan.html"; // or "index.html" or wherever you want to send them
  }, 300); // short delay to let the alert finish
}
</script>

<body>

<header>
  <h1>DreamSpot</h1>
  <p>Your Vacation Search Results</p>
</header>

<main>
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='result-box'>";
        echo "<img src='" . htmlspecialchars($row['hotel_image']) . "' alt='Hotel image'>";
        echo "<h3>" . htmlspecialchars($row['destination']) . " – " . htmlspecialchars($row['hotel_name']) . "</h3>";
        echo "<p><strong>Flight:</strong> " . htmlspecialchars($row['flight_company']) . "</p>";
        echo "<p><strong>Dates:</strong> " . htmlspecialchars($row['departure_date']) . " to " . htmlspecialchars($row['return_date']) . "</p>";
        echo "<p><strong>Accommodation:</strong> " . htmlspecialchars($row['accommodation_type']) . ", Class: " . htmlspecialchars($row['flight_class']) . "</p>";
        echo "<p><strong>Price per person:</strong> €" . $row['price'] . "</p>";
        echo "<p><strong>Total for $people traveler(s):</strong> €" . ($row['price'] * $people) . "</p>";
        echo "<button onclick='confirmBooking(\"" . htmlspecialchars($row['destination']) . "\")'>Book Now</button>";
        echo "</div>";
    }
} else {
    echo "<p class='no-results'>No vacations found matching your criteria. Try different filters.</p>";
}
$conn->close();
?>
</main>

<footer>
  <p>📍 Bulevardul Unirii, Bucharest, Romania | 📞 +40 700 123 456 | 📧 contact@dreamspot.ro</p>
</footer>

</body>
</html>
