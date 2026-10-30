<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'PHP MVC') ?></title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 3rem auto; padding: 0 1rem; line-height: 1.6; color: #222; }
        code { background: #f2f2f2; padding: 0.1rem 0.35rem; border-radius: 4px; }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>
