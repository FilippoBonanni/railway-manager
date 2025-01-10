<?php
// Results per page
$results_for_page = 5;

// Calculate total pages
$sql_count = "SELECT COUNT(*) as total FROM Richiesta";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_results = $row_count['total'];
$total_pages = ceil($total_results / $results_for_page);

// Determine current page
$current_page = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
}

// Calculate offset for query
$offset = ($current_page - 1) * $results_for_page;

// Query to get current page items
$sql = "SELECT * FROM Richiesta ORDER BY Data DESC LIMIT $results_for_page OFFSET $offset";
$result = $conn->query($sql);
?>