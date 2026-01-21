<?php
// ============================================================================
// AUTH.PHP - Prosty system uwierzytelniania i RBAC
// Data: 2026-01-21
// Wersja: 2.0
// ============================================================================

session_start();

// Funkcja logowania
function login($username, $password, $conn) {
    $password_hash = hash('sha256', $password);
    
    $sql = "SELECT u.ID_USER, u.USERNAME, u.EMAIL, u.FIRST_NAME, u.LAST_NAME, 
                   u.ROLE_CODE, r.NAME as ROLE_NAME, r.PERMISSIONS, u.STATUS_CODE
            FROM USERS u
            JOIN DICT_USER_ROLE r ON u.ROLE_CODE = r.CODE
            WHERE u.USERNAME = :username AND u.PASSWORD_HASH = :password_hash";
    
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':username', $username);
    oci_bind_by_name($stmt, ':password_hash', $password_hash);
    
    if (oci_execute($stmt)) {
        $user = oci_fetch_assoc($stmt);
        
        if ($user) {
            if ($user['STATUS_CODE'] !== 'ACTIVE') {
                return ['success' => false, 'message' => 'Konto jest nieaktywne lub zablokowane'];
            }
            
            // Zapisz dane użytkownika w sesji
            $_SESSION['user_id'] = $user['ID_USER'];
            $_SESSION['username'] = $user['USERNAME'];
            $_SESSION['email'] = $user['EMAIL'];
            $_SESSION['first_name'] = $user['FIRST_NAME'];
            $_SESSION['last_name'] = $user['LAST_NAME'];
            $_SESSION['role_code'] = $user['ROLE_CODE'];
            $_SESSION['role_name'] = $user['ROLE_NAME'];
            $_SESSION['permissions'] = $user['PERMISSIONS'];
            $_SESSION['logged_in'] = true;
            
            // Aktualizuj last_login
            $update_sql = "UPDATE USERS SET LAST_LOGIN = SYSTIMESTAMP WHERE ID_USER = :user_id";
            $update_stmt = oci_parse($conn, $update_sql);
            oci_bind_by_name($update_stmt, ':user_id', $user['ID_USER']);
            oci_execute($update_stmt);
            oci_commit($conn);
            
            return ['success' => true, 'user' => $user];
        }
    }
    
    return ['success' => false, 'message' => 'Nieprawidłowa nazwa użytkownika lub hasło'];
}

// Funkcja wylogowania
function logout() {
    session_unset();
    session_destroy();
}

// Sprawdź czy użytkownik jest zalogowany
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Wymagaj zalogowania
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Sprawdź uprawnienia
function has_permission($permission) {
    if (!is_logged_in()) {
        return false;
    }
    
    // Admin ma wszystkie uprawnienia
    if ($_SESSION['role_code'] === 'ADMIN') {
        return true;
    }
    
    // Sprawdź czy użytkownik ma dane uprawnienie
    $permissions = explode(',', $_SESSION['permissions']);
    
    foreach ($permissions as $perm) {
        if (trim($perm) === $permission || trim($perm) === 'all') {
            return true;
        }
        
        // Sprawdź wildcard (np. crm.* obejmuje crm.read, crm.write)
        $perm_parts = explode('.', trim($perm));
        $required_parts = explode('.', $permission);
        
        if (count($perm_parts) == 2 && $perm_parts[1] === '*' && $perm_parts[0] === $required_parts[0]) {
            return true;
        }
    }
    
    return false;
}

// Wymagaj uprawnienia
function require_permission($permission) {
    if (!has_permission($permission)) {
        header('HTTP/1.0 403 Forbidden');
        die('<h1>403 Forbidden</h1><p>Brak uprawnień do tej operacji.</p>');
    }
}

// Sprawdź rolę
function has_role($role_code) {
    if (!is_logged_in()) {
        return false;
    }
    
    if (is_array($role_code)) {
        return in_array($_SESSION['role_code'], $role_code);
    }
    
    return $_SESSION['role_code'] === $role_code;
}

// Wymagaj roli
function require_role($role_code) {
    if (!has_role($role_code)) {
        header('HTTP/1.0 403 Forbidden');
        die('<h1>403 Forbidden</h1><p>Brak uprawnień. Wymagana rola: ' . (is_array($role_code) ? implode(' lub ', $role_code) : $role_code) . '</p>');
    }
}

// Pobierz informacje o zalogowanym użytkowniku
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'role_code' => $_SESSION['role_code'],
        'role_name' => $_SESSION['role_name'],
        'permissions' => $_SESSION['permissions']
    ];
}

// Pobierz ID zalogowanego użytkownika
function get_current_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : null;
}

// Pobierz pełne imię użytkownika
function get_current_user_full_name() {
    if (!is_logged_in()) {
        return 'Gość';
    }
    
    return trim($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
}

// CSRF Token (opcjonalne, ale zalecane)
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
