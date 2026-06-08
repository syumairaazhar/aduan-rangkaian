<?php
// Include necessary files for session handling and database connection
include('auth_check.php');  // Ensure the user is authenticated to access this page

// Initialize filter variables from GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build the query with filters
$query = "SELECT ticket_id, id_number, category, description, location, status, last_updated FROM tickets WHERE 1";
$params = [];

if ($search) {
    if (preg_match('/^[a-zA-Z0-9]+$/', $search)) {
        $query .= " AND id_number LIKE ?";
        $params[] = '%' . $search . '%';
    } else {
        $query .= " AND category LIKE ?";
        $params[] = '%' . $search . '%';
    }
}

if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if ($category_filter) {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}

if ($date_filter) {
    $query .= " AND DATE(last_updated) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY last_updated DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Set the file name
$filename = "Ticket_Report_" . date('Y-m-d_H-i-s') . ".csv";

// Set the header to indicate the file is a CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Write the header row
fputcsv($output, ['Ticket ID', 'ID Number', 'Category', 'Description', 'Location', 'Status', 'Last Updated']);

// Fetch rows and write to the CSV
while ($row = $result->fetch_assoc()) {
    $formatted_row = array_map(function ($value, $key) {
        if ($key === 'id_number') {
            return strtoupper($value);
        }
        return ucwords(strtolower($value));
    }, $row, array_keys($row));

    fputcsv($output, $formatted_row);
}

fclose($output);

$stmt->close();
$conn->close();
