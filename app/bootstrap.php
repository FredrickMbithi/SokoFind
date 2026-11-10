<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
if (is_file(__DIR__ . '/config.local.php')) require __DIR__ . '/config.local.php';
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Africa/Nairobi');

function env_value(string $key, string $fallback = ''): string {
    $value = getenv($key);
    if ($value !== false) return $value;
    $constant = 'APP_' . $key;
    return defined($constant) ? (string)constant($constant) : $fallback;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', env_value('DB_HOST', '127.0.0.1'), env_value('DB_PORT', '3306'), env_value('DB_NAME', 'sokofind'));
    $pdo = new PDO($dsn, env_value('DB_USER', 'root'), env_value('DB_PASS', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path); exit; }
function signed_in(): bool { return isset($_SESSION['user']['id']); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!signed_in()) { flash('Please sign in to continue.', 'info'); redirect('login.php'); } }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419); exit('Your session expired. Go back, reload, and try again.');
    }
}
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = ['message' => $message, 'type' => $type]; }
function take_flash(): ?array { $item = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $item; }
function categories(): array { return ['Electronics', 'Books', 'Clothing', 'Keys & Cards', 'Bags', 'Documents', 'Other']; }
function valid_category(string $value): bool { return in_array($value, categories(), true); }
function format_date(string $value): string { return date('M j, Y', strtotime($value)); }

function page_start(string $title, string $active = ''): void {
    $user = current_user(); $message = take_flash();
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="A campus lost-and-found board for Kirinyaga University"><title><?= e($title) ?> · SokoFind</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <header class="topbar"><a class="brand" href="index.php"><span class="brand-mark">S</span><span>SokoFind<small>Kirinyaga University</small></span></a><button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button><nav class="nav"><a class="<?= $active === 'browse' ? 'active' : '' ?>" href="index.php">Browse items</a><?php if ($user): ?><a class="<?= $active === 'mine' ? 'active' : '' ?>" href="my-items.php">My items</a><a class="<?= $active === 'reports' ? 'active' : '' ?>" href="reports.php">Reports</a><a class="button button-small" href="item-form.php">＋ Post an item</a><span class="nav-user"><?= e($user['name']) ?></span><form class="nav-logout" method="post" action="logout.php"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button>Log out</button></form><?php else: ?><a href="login.php">Sign in</a><a class="button button-small" href="register.php">Create account</a><?php endif; ?></nav></header>
    <main class="container"><?php if ($message): ?><div class="flash flash-<?= e($message['type']) ?>" role="status"><?= e($message['message']) ?></div><?php endif; ?>
    <?php
}
function page_end(): void { ?><footer class="footer"><span>SokoFind · Student-built campus community project</span><span>Share details safely. Meet in a public campus location.</span></footer><script src="assets/app.js" defer></script></main></body></html><?php }
