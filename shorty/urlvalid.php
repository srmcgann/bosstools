<?php
  $url = $_GET['url'];
  echo json_encode([filter_var($url, FILTER_VALIDATE_URL) !== false]);
?>
