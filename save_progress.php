<?php
// save_progress.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$book_id = intval($input['book_id'] ?? 0);
$current_page = intval($input['current_page'] ?? 1);
$total_pages = intval($input['total_pages'] ?? 100);
$progress_percentage = intval($input['progress_percentage'] ?? 0);

if ($book_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid book ID']);
    exit();
}

try {
    // Update atau insert progress
    $sql = "INSERT INTO reading_progress (user_id, book_id, current_page, total_pages, progress_percentage, last_read, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW()) 
            ON DUPLICATE KEY UPDATE 
            current_page = VALUES(current_page),
            total_pages = VALUES(total_pages),
            progress_percentage = VALUES(progress_percentage),
            last_read = NOW(),
            updated_at = NOW()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $user_id, $book_id, $current_page, $total_pages, $progress_percentage);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Progress saved successfully',
            'data' => [
                'current_page' => $current_page,
                'progress_percentage' => $progress_percentage
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save progress']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>