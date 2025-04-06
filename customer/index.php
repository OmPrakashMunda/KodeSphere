<?php
include '../utils/php/db.php';
session_start();

if (!isset($_SESSION['customer_logged_in'])) {
  header('Location: auth.php');
  exit();
}

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';

$query = "SELECT * FROM businesses WHERE 1";
$params = [];

if (!empty($search)) {
  $query .= " AND (business_name LIKE ? OR business_type LIKE ?)";
  $searchParam = "%$search%";
  $params[] = $searchParam;
  $params[] = $searchParam;
}

if (!empty($type)) {
  $query .= " AND business_type = ?";
  $params[] = $type;
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
  $types = str_repeat("s", count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$businesses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookEasy Dashboard</title>
  <style>
    :root {
      --primary: #1E0D73;
      --secondary: #FF9800;
      --accent: #B7BDB7;
      --background: #F4F1EB;
      --text-dark: #050315;
      --text-light: #F4F1EB;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--background);
      color: var(--text-dark);
    }

    .header {
      text-align: center;
      margin: 32px 0 24px;
      font-size: 28px;
      color: var(--text-dark);
    }

    .filter-bar {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 32px;
      flex-wrap: wrap;
      padding: 0 16px;
    }

    .filter-bar input,
    .filter-bar select,
    .filter-bar button {
      padding: 10px 14px;
      border: 1px solid var(--accent);
      font-size: 15px;
      background-color: white;
      color: var(--text-dark);
      outline: none;
      border-radius: 0;
    }

    .filter-bar button {
      background-color: var(--primary);
      color: var(--text-light);
      cursor: pointer;
    }

    .business-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      padding: 0 32px 64px;
    }

    .card {
      background-color: white;
      border: 1px solid var(--accent);
      padding: 16px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: box-shadow 0.2s ease;
      min-height: 420px;
    }

    .card:hover {
      box-shadow: 0 0 0 2px var(--primary);
    }

    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .card h3 {
      font-size: 20px;
      margin: 8px 0;
      color: var(--primary);
    }

    .card p {
      margin: 4px 0;
      font-size: 14px;
      color: var(--text-dark);
    }

    .book-btn {
      margin-top: 12px;
      width: 100%;
      padding: 10px;
      background-color: var(--secondary);
      color: var(--text-light);
      border: none;
      font-size: 15px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .book-btn:hover {
      background-color: #e68900;
    }

    @media (max-width: 1000px) {
      .business-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .business-grid {
        grid-template-columns: 1fr;
      }

      .navbar h1 {
        font-size: 18px;
      }

      .logout-btn {
        padding: 8px 12px;
        font-size: 12px;
      }

      .header {
        font-size: 24px;
      }
    }
  </style>
  <style>
  .navbar {
    background-color: #1E0D73;
    color: white;
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .navbar .logo {
    font-weight: bold;
    font-size: 20px;
    letter-spacing: 1px;
  }

  .navbar a.logout-btn {
    background-color: white;
    color: #FF9800;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    transition: 0.2s;
  }

  .navbar a.logout-btn:hover {
    background-color: #eee;
  }
</style>
</head>
<body>
<div class="navbar">
  <div class="logo">BookEasy</div>
  <a href="logout.php" class="logout-btn">Logout</a>
</div>

  <h2 class="header">Explore Businesses</h2>

  <form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Search by name or type" value="<?= htmlspecialchars($search) ?>">
    <select name="type">
      <option value="">All Types</option>
      <option value="Salon" <?= $type == 'Salon' ? 'selected' : '' ?>>Salon</option>
      <option value="Clinic" <?= $type == 'Clinic' ? 'selected' : '' ?>>Clinic</option>
      <option value="Gym" <?= $type == 'Gym' ? 'selected' : '' ?>>Gym</option>
      <option value="Other" <?= $type == 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
    <button type="submit">Filter</button>
  </form>

  <div class="business-grid">
    <?php foreach ($businesses as $biz): ?>
      <div class="card">
        <div>
          <img src="<?= '../businesses/images/'. $biz['image'] ? '../businesses/images/'. htmlspecialchars($biz['image']) : 'https://picsum.photos/seed/picsum/300/180' ?>" alt="Business Image">
          <h3><?= htmlspecialchars($biz['business_name']) ?></h3>
          <p><strong>Type:</strong> <?= htmlspecialchars($biz['business_type']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($biz['phone']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($biz['email']) ?></p>
          <p><strong>Working Days:</strong> <?= htmlspecialchars($biz['working_days']) ?></p>
          <p><strong>Working Hours:</strong> <?= htmlspecialchars($biz['working_hours']) ?></p>
        </div>
        <form action="book.php" method="get">
          <input type="hidden" name="business_id" value="<?= $biz['id'] ?>">
          <button type="submit" class="book-btn">Book Appointment</button>
        </form>
      </div>
    <?php endforeach; ?>

    <?php
      // Fill empty cards to maintain 3 per row layout
      $remainder = count($businesses) % 3;
      if ($remainder !== 0) {
        for ($i = 0; $i < 3 - $remainder; $i++) {
          echo '<div class="card" style="visibility:hidden;"></div>';
        }
      }
    ?>
  </div>
</body>
</html>
