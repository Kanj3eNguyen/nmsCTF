<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
</head>
<body>
    <?php
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(dirname($scriptName), '/');
    if ($basePath === '' || $basePath === '.') {
        $basePath = '';
    }
    ?>

    <h2>Đăng ký</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($basePath . '/signup', ENT_QUOTES, 'UTF-8'); ?>" method="POST">
        <input type="text" name="username" placeholder="username"><br><br>
        <input type="email" name="email" placeholder="Email"><br><br>
        <input type="password" name="password" placeholder="Mật khẩu"><br><br>
        <button type="submit">Đăng ký</button>
    </form>

    <p><a href="<?php echo htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8'); ?>">Đã có tài khoản? Đăng nhập</a></p>
</body>
</html>