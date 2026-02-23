<?php
/**
 * Native .env file loader — no external packages required.
 * Reads key=value pairs and stores them in $_ENV and putenv().
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Split on first = only (value may contain =)
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        // Remove surrounding quotes if present
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Store in both $_ENV and putenv
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }

    return true;
}
?>
