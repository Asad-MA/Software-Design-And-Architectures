<form action="<?php echo e(url('/register')) ?>" method="post">
    <h1>Register</h1>

    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Register</button>
    
</form>