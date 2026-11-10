<?php
require __DIR__ . '/../app/bootstrap.php';
$term = trim((string)($_GET['q'] ?? ''));
$kind = (string)($_GET['kind'] ?? '');
$category = (string)($_GET['category'] ?? '');
$conditions = ["i.status = 'Open'"]; $params = [];
if ($term !== '') { $conditions[] = '(i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)'; $like = '%' . $term . '%'; array_push($params, $like, $like, $like); }
if (in_array($kind, ['Lost', 'Found'], true)) { $conditions[] = 'i.kind = ?'; $params[] = $kind; } else $kind = '';
if (valid_category($category)) { $conditions[] = 'i.category = ?'; $params[] = $category; } else $category = '';
$sql = 'SELECT i.*, u.name AS poster_name FROM items i JOIN users u ON u.id=i.user_id WHERE ' . implode(' AND ', $conditions) . ' ORDER BY i.created_at DESC LIMIT 60';
$stmt = db()->prepare($sql); $stmt->execute($params); $items = $stmt->fetchAll();
page_start('Find what matters', 'browse');
?><section class="hero"><div class="hero-copy"><span class="eyebrow">LOST SOMETHING? FOUND SOMETHING?</span><h1>Good things find their way back.</h1><p>A simple campus board to report lost belongings and reunite found items with their owners.</p><a class="button" href="<?= signed_in() ? 'item-form.php' : 'register.php' ?>">＋ Post an item</a></div><div class="hero-art" aria-hidden="true"><div class="orbit orbit-a"></div><div class="orbit orbit-b"></div><div class="hero-icon">⌕</div><span class="float-tag tag-one">ID card</span><span class="float-tag tag-two">Notebook</span><span class="float-tag tag-three">Keys</span></div></section>
<section class="section-head"><div><span class="eyebrow">CAMPUS BOARD</span><h2>Recently reported</h2></div><span class="muted"><?= count($items) ?> open <?= count($items) === 1 ? 'listing' : 'listings' ?></span></section>
<form class="filter-bar" method="get"><label class="search-field"><span>⌕</span><input name="q" value="<?= e($term) ?>" placeholder="Search item, description or location" aria-label="Search listings"></label><select name="kind" aria-label="Filter by item type"><option value="">All item types</option><option <?= $kind === 'Lost' ? 'selected' : '' ?>>Lost</option><option <?= $kind === 'Found' ? 'selected' : '' ?>>Found</option></select><select name="category" aria-label="Filter by category"><option value="">All categories</option><?php foreach (categories() as $c): ?><option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select><button class="button button-dark">Search</button></form>
<?php if (!$items): ?><div class="empty-state"><span>⌕</span><h3>No open listings match</h3><p>Try a different search, or be the first to report an item.</p></div><?php else: ?><div class="item-grid"><?php foreach ($items as $item) include __DIR__ . '/../app/item-card.php'; ?></div><?php endif; ?>
<?php page_end(); ?>
