<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $conn = new mysqli("localhost", "root", "", "calculators");
  if (!$conn->connect_error) {
    $stmt = $conn->prepare("DELETE FROM result WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
  }
}
header("Location: index.php");
exit;
