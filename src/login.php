<?php
include "database.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Будь ласка, введіть логін та пароль.';
    } else {
        try {
            $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: /');
                exit();
            } else {
                $error = 'Невірний логін або пароль';
            }
        } catch (PDOException $e) {
            $error = 'Не вдалось увійти. Спробуйте знову.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #dc3545;
            border-radius: 4px;
            background-color: #f8d7da;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .links a {
            color: #007bff;
            text-decoration: none;
        }

        .demo-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>

    <div class="demo-info">
        <strong>Демо адмін аккаунт:</strong><br>
        Логін: admin <br>Пароль: admin123<br>
    </div>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="loginForm" method="POST">
        <div class="form-group">
            <label for="username">Логін або електронна пошта:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Увійти</button>
    </form>

    <div class="links">
        <a href="/register/">Не маєте аккаунту? Зареєструватись</a>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (!username || !password) {
            e.preventDefault();
            alert('Будь ласка, введіть логін та пароль!');
        }
    });
</script>
</body>
</html>