<?php
// Results per page
$results_for_page = 5;

// Calculate total pages
$sql_count = "SELECT COUNT(*) as total FROM Tratta";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_results = $row_count['total'];
$total_pages = ceil($total_results / $results_for_page);

// Current date
$current_date = date('Y-m-d H:i:s');

// Find route with current date departure
$sql_today = "SELECT * FROM Tratta WHERE DATE(DataPartenza) = DATE('$current_date') ORDER BY DataPartenza ASC LIMIT 1";
$result_today = $conn->query($sql_today);

if ($result_today->num_rows > 0) {
    // If route exists for current date
    $row_today = $result_today->fetch_assoc();
    $route_today_date = $row_today['DataPartenza'];

    // Find page position for current route
    $sql_count_today = "SELECT COUNT(*) as pos FROM Tratta WHERE DataPartenza < '$route_today_date'";
    $result_count_today = $conn->query($sql_count_today);
    $row_count_today = $result_count_today->fetch_assoc();
    $route_today_position = $row_count_today['pos'];

    // Calculate current page
    $current_page = ceil(($route_today_position + 1) / $results_for_page);
} else {
    // Find closest route if no current date route exists
    $sql_closest = "
       (SELECT * FROM Tratta WHERE DataPartenza > '$current_date' ORDER BY DataPartenza ASC LIMIT 1)
       UNION
       (SELECT * FROM Tratta WHERE DataPartenza < '$current_date' ORDER BY DataPartenza DESC LIMIT 1)
       ORDER BY DataPartenza ASC LIMIT 1";
    $result_closest = $conn->query($sql_closest);

    if ($result_closest->num_rows > 0) {
        $row_closest = $result_closest->fetch_assoc();
        $route_closest_date = $row_closest['DataPartenza'];

        // Find page position for closest route
        $sql_count_closest = "SELECT COUNT(*) as pos FROM Tratta WHERE DataPartenza < '$route_closest_date'";
        $result_count_closest = $conn->query($sql_count_closest);
        $row_count_closest = $result_count_closest->fetch_assoc();
        $route_closest_position = $row_count_closest['pos'];

        // Calculate current page
        $current_page = ceil(($route_closest_position + 1) / $results_for_page);
    } else {
        // Default to first page if no routes exist
        $current_page = 1;
    }
}

// Use GET page if specified
if (isset($_GET['pagina'])) {
    $current_page = (int)$_GET['pagina'];
}

// Calculate query offset
$offset = ($current_page - 1) * $results_for_page;

// Query for current page items
$sql = "SELECT * FROM Tratta 
       ORDER BY DataPartenza ASC 
       LIMIT $results_for_page OFFSET $offset";
$result = $conn->query($sql);
?>