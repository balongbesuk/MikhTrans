<?php
/**
 * CSRF Protection Helper untuk MikhTrans
 * Mencegah serangan Cross-Site Request Forgery pada semua form POST.
 * Kompatibel dengan PHP 5.x ke atas.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Polyfill hash_equals untuk PHP < 5.6
if (!function_exists('hash_equals')) {
    function hash_equals($str1, $str2) {
        if (strlen($str1) !== strlen($str2)) {
            return false;
        }
        $res = $str1 ^ $str2;
        $ret = 0;
        for ($i = strlen($res) - 1; $i >= 0; $i--) {
            $ret |= ord($res[$i]);
        }
        return !$ret;
    }
}

// Polyfill password hashing untuk PHP < 5.5
if (!defined('PASSWORD_BCRYPT')) {
    define('PASSWORD_BCRYPT', 1);
}
if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
}

if (!function_exists('password_hash')) {
    function password_hash($password, $algo, array $options = array()) {
        if ($algo !== 1 && $algo !== '2y' && $algo !== PASSWORD_BCRYPT) {
            trigger_error("password_hash(): Unknown password hashing algorithm", E_USER_WARNING);
            return null;
        }
        
        $cost = isset($options['cost']) ? (int)$options['cost'] : 10;
        if ($cost < 4 || $cost > 31) {
            trigger_error("password_hash(): Invalid bcrypt cost parameter specified", E_USER_WARNING);
            return null;
        }
        
        // Generate 16 bytes of entropy
        $entropy = "";
        if (function_exists('random_bytes')) {
            $entropy = random_bytes(16);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $entropy = openssl_random_pseudo_bytes(16);
        } else {
            $entropy = uniqid(mt_rand(), true);
        }
        
        // Encode using bcrypt-compatible base64 alphabet
        $salt_base64 = base64_encode($entropy);
        $salt_base64 = str_replace('+', '.', $salt_base64); // bcrypt expects '.' instead of '+'
        $salt_base64 = substr($salt_base64, 0, 22);
        
        $prefix = sprintf("$2y$%02d$", $cost);
        return crypt($password, $prefix . $salt_base64);
    }
}

if (!function_exists('password_verify')) {
    function password_verify($password, $hash) {
        if (!is_string($password) || !is_string($hash)) {
            return false;
        }
        return hash_equals($hash, crypt($password, $hash));
    }
}


/**
 * Generate atau ambil token CSRF yang aktif.
 * Token berlaku selama sesi berlangsung.
 * @return string Token CSRF
 */
function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        if (function_exists('random_bytes')) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $_SESSION['_csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            // Robust fallback for old PHP versions or environments without OpenSSL
            $entropy = uniqid(mt_rand(), true) . microtime() . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . getmypid();
            $_SESSION['_csrf_token'] = hash('sha256', $entropy);
        }
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Output hidden input field berisi token CSRF.
 * Panggil di dalam <form> tag.
 * @return string HTML hidden input
 */
function csrf_field() {
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verifikasi token CSRF dari POST request.
 * Jika gagal, return HTTP 403 dan hentikan eksekusi.
 * @return bool true jika valid
 */
function csrf_verify() {
    $token = isset($_POST['_csrf_token']) ? $_POST['_csrf_token'] : '';
    if (empty($token) || !isset($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], $token)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>Invalid or missing CSRF token. Silakan muat ulang halaman dan coba lagi.</p>';
        exit;
    }
    return true;
}
