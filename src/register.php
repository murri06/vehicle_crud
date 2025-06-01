<?php
require_once 'database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Всі поля необхідно заповнити.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Паролі не співпадають.';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль має бути мінімум 6 символів.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->fetch()) {
                $error = 'Логін або електронна пошта уже зайняті.';
            } else {
                // Insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword]);

                $success = 'Реєстрація успішна! Тепер ви можете увійти в свій аккаунт.';
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            $error = 'Реєстрація не пройшла успішно. Будь ласка, спробуйте ще раз.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
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

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #dc3545;
            border-radius: 4px;
            background-color: #f8d7da;
        }

        .success {
            color: #155724;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #28a745;
            border-radius: 4px;
            background-color: #d4edda;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .links a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Реєстрація</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form id="registerForm" method="POST">
        <div class="form-group">
            <label for="username">Логін:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Повторіть пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit">Реєстрація</button>
    </form>

    <div class="links">
        <a href="login.php">Уже маєте аккаунт? Увійти.</a>
    </div>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
        }

        if (password.length < 6) {
            e.preventDefault();
            alert('Пароль має бути мінімум 6 символів!');
        }
    });
</script>
</body>
</html>