<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Business Dashboard - BookEasy</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://kit.fontawesome.com/f94f9b7388.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #F4F1EB;
      color: #050315;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #1E0D73;
    }

    .profile-btn {
      background-color: #FF9800;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    .container.dashboard-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .business-profile {
      margin-top: 30px;
    }

    .image-upload-section {
      margin-bottom: 20px;
    }

    #business-image {
      max-width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 10px;
      border: 2px solid #ccc;
    }

    .business-details-form input,
    .business-details-form textarea,
    .business-details-form select {
      display: block;
      width: 100%;
      margin: 10px 0;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    .business-details-form button {
      background-color: #1E0D73;
      color: #fff;
      border: none;
      padding: 12px 20px;
      font-size: 16px;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .business-details-form button:hover {
      background-color: #15095d;
    }

    .booking-list {
      margin-top: 40px;
      text-align: left;
    }

    .booking-item {
      border: 1px solid #ccc;
      padding: 16px;
      margin-bottom: 15px;
      border-radius: 10px;
      background-color: #fcfcfc;
      transition: box-shadow 0.2s;
    }

    .booking-item:hover {
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .booking-item strong {
      color: #1E0D73;
    }

    .booking-item button {
      margin-top: 10px;
      padding: 8px 12px;
      background-color: #e53935;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .booking-item button:hover {
      background-color: #c62828;
    }
  </style>
</head>

<body>
  <?php
  session_start();
  if (!isset($_SESSION['business_logged_in'])) {
    header('Location: login.php');
    exit();
  }

  include '../utils/php/db.php';
  $business_email = $_SESSION['business_email'];

  $stmt = $conn->prepare("SELECT * FROM businesses WHERE email = ?");
  $stmt->bind_param("s", $business_email);
  $stmt->execute();
  $result = $stmt->get_result();
  $business = $result->fetch_assoc();
  $stmt->close();

  $business_id = $business['id'];

  $booking_stmt = $conn->prepare("SELECT * FROM bookings WHERE business_id = ? ORDER BY slot_time DESC");
  $booking_stmt->bind_param("i", $business_id);
  $booking_stmt->execute();
  $bookings_result = $booking_stmt->get_result();
  ?>

  <nav>
    <div class="logo">BookEasy</div>
    <a href="logout.php"><button class="profile-btn"><i class="fa-solid fa-right-from-bracket"></i></button></a>
  </nav>

  <div class="container dashboard-container">
    <h1>Welcome, <span id="business-name"><?php echo htmlspecialchars($business['full_name']); ?></span></h1>
    <div class="business-profile">
      <form class="business-details-form" method="POST" action="update_business.php" enctype="multipart/form-data">
        <div class="image-upload-section">
          <img src="<?php echo file_exists($business['image']) ? $business['image'] : '../assets/default-business.jpg'; ?>" alt="Business Image" id="business-image">
          <input type="file" id="upload-image" name="image" accept="image/*">
        </div>

        <input type="text" name="name" value="<?php echo htmlspecialchars($business['business_name']); ?>" placeholder="Business Name" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($business['phone']); ?>" placeholder="Phone Number" required>
        <textarea name="slots" placeholder="Describe available slots, timings, etc..." required><?php echo htmlspecialchars($business['slots']); ?></textarea>

        <label for="working_days">Working Days:</label>
        <select name="working_days" required>
          <option value="Mon-Fri" <?php if ($business['working_days'] == 'Mon-Fri') echo 'selected'; ?>>Mon-Fri</option>
          <option value="Mon-Sat" <?php if ($business['working_days'] == 'Mon-Sat') echo 'selected'; ?>>Mon-Sat</option>
          <option value="Mon-Sun" <?php if ($business['working_days'] == 'Mon-Sun') echo 'selected'; ?>>Mon-Sun</option>
          <option value="Weekends" <?php if ($business['working_days'] == 'Weekends') echo 'selected'; ?>>Weekends</option>
        </select>

        <label for="working_hours_start">Working Hours:</label>
        <div style="display: flex; gap: 10px;">
          <select name="working_hours_start" required>
            <?php
            $times = ["6 AM", "7 AM", "8 AM", "9 AM", "10 AM", "11 AM", "12 PM", "1 PM", "2 PM", "3 PM", "4 PM", "5 PM", "6 PM", "7 PM", "8 PM", "9 PM"];
            foreach ($times as $time) {
              $selected = ($business['working_hours_start'] ?? '') == $time ? 'selected' : '';
              echo "<option value=\"$time\" $selected>$time</option>";
            }
            ?>
          </select>

          <span style="align-self: center;">to</span>

          <select name="working_hours_end" required>
            <?php
            foreach ($times as $time) {
              $selected = ($business['working_hours_end'] ?? '') == $time ? 'selected' : '';
              echo "<option value=\"$time\" $selected>$time</option>";
            }
            ?>
          </select>
        </div>

        <button type="submit">Update Details</button>
      </form>
    </div>

    <div class="booking-list">
      <h2>Bookings</h2>
      <?php while ($booking = $bookings_result->fetch_assoc()): ?>
        <div class="booking-item" id="booking-<?php echo $booking['id']; ?>">
          <p><strong>Booking Code:</strong> <?php echo htmlspecialchars($booking['booking_code']); ?></p>
          <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['customer_phone']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['customer_email']); ?></p>
          <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['slot_time']); ?></p>
          <p><strong>Service:</strong> <?php echo htmlspecialchars($booking['service']); ?></p>
          <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?></p>
          <button class="delete-booking-btn" data-id="<?php echo $booking['id']; ?>">Delete Booking</button>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $('#upload-image').on('change', function (e) {
        const reader = new FileReader();
        reader.onload = function (e) {
          $('#business-image').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      });

      $('.delete-booking-btn').click(function () {
        const bookingId = $(this).data('id');
        if (confirm('Are you sure you want to delete this booking?')) {
          $.ajax({
            url: 'delete_booking.php',
            type: 'POST',
            data: { id: bookingId },
            success: function (response) {
              if (response === 'success') {
                $('#booking-' + bookingId).remove();
              } else {
                alert('Failed to delete booking.');
              }
            }
          });
        }
      });
    });
  </script>
</body>

</html>