require_once 'db.php';

// Get connection instance
$db = Database::getInstance();
$conn = $db->getConnection();
$db->setCharset('utf8mb4');

// Set charset to UTF-8 to support special characters
$conn->set_charset('utf8mb4');

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) === 'connect.php') {
    die('Direct access not allowed');
}
?>