<h1>Error <?= e($status) ?></h1>
<p>Something went wrong.</p>
<?php if (!empty($details)): ?>
    <pre><?= e($details) ?></pre>
<?php endif; ?>
