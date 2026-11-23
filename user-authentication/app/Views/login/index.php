<?php if (!empty($error)): ?>
    <p class="error" role="alert"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST" action="<?php echo e(url('/login')) ?>">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>