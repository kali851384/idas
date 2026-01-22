<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="login.php">
    <label>E-Mail:</label><br/>
    <input type="email" name="email" required><br/>

    <label>Passwort:</label><br/>
    <input type="password" name="password" required><br/><br/>

    <input type="submit" value="Login">
</form>

<a href="signin.php">Noch keinen Account? Registrieren</a>

</body>
</html>
