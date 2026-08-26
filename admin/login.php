<?php
require_once __DIR__ . '/config.php';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Construction Helps</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../images/construction/favicon.svg" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #104cba 0%, #0d3888 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: #f8fafc;
            padding: 30px 25px 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .login-header h2 {
            margin: 10px 0 5px;
            color: #104cba;
            font-size: 22px;
            font-weight: 700;
        }
        .login-header p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }
        .login-body {
            padding: 30px 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }
        .input-group {
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 14px;
            top: 13px;
            color: #94a3b8;
            font-size: 15px;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #104cba;
            box-shadow: 0 0 0 3px rgba(16, 76, 186, 0.15);
        }
        .btn-submit {
            width: 100%;
            background: #104cba;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-submit:hover {
            background: #0d3888;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-footer {
            background: #f8fafc;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="../images/construction/construction_logo.svg" alt="Construction Helps" style="max-height: 48px;">
        <h2>Admin Portal</h2>
        <p>Sign in to access candidate bookings &amp; form submissions</p>
    </div>

    <div class="login-body">
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username / ID</label>
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required autofocus autocomplete="username" spellcheck="false">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Dashboard
            </button>
        </form>
    </div>

    <div class="login-footer">
        <a href="../index.html" style="color: #104cba; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Return to Website</a>
    </div>
</div>

</body>
</html>
