<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - CRM Warsztat</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .login-card h2 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: bold;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🚗 CRM Warsztat</h2>
    
    <?php
    require_once 'config.php';
    require_once 'auth.php';
    
    // Jeśli już zalogowany, przekieruj
    if (is_logged_in()) {
        header('Location: index.php');
        exit;
    }
    
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($password)) {
            $result = login($username, $password, $conn);
            
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
            }
        } else {
            $error = 'Proszę wypełnić wszystkie pola';
        }
    }
    ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="username">Nazwa użytkownika</label>
            <input type="text" class="form-control" id="username" name="username" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Hasło</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-login btn-block">Zaloguj się</button>
    </form>
    
    <hr class="my-4">
    
    <div class="text-center text-muted small">
        <p><strong>Konta testowe:</strong></p>
        <p>admin / admin123<br>
        sprzedawca / admin123<br>
        serwisant / admin123</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
