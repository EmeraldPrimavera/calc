<?php
$conn = new mysqli("localhost", "root", "", "calculators");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$existing = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = intval($_POST["id"]);
  $updatedResult = $_POST["result"];

  $stmt = $conn->prepare("UPDATE result SET results = ? WHERE id = ?");
  $stmt->bind_param("si", $updatedResult, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: index.php");
  exit;
}

// Fetch current result
$stmt = $conn->prepare("SELECT results FROM result WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($existing);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
  <div class="container">
    <h2 class="mb-4 text-center">Edit Calculation Result</h2>
    <form method="POST" class="card p-4 shadow-sm">
      <input type="hidden" name="id" value="<?= $id ?>">
      <div class="mb-3">
        <label for="result" class="form-label">Result</label>
        <input type="text" name="result" class="form-control" value="<?= htmlspecialchars($existing) ?>" required>
      </div>
      <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</body>
</html>

<?php $conn->close(); ?>
