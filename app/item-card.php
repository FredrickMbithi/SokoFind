<?php
/** @var array $item */
$kindClass = $item['kind'] === 'Lost' ? 'badge-lost' : 'badge-found';
?><article class="item-card"><div class="item-card-top"><span class="badge <?= $kindClass ?>"><?= e($item['kind']) ?></span><span class="category"><?= e($item['category']) ?></span></div><h3><a href="item.php?id=<?= (int)$item['id'] ?>"><?= e($item['title']) ?></a></h3><p><?= e(mb_strimwidth($item['description'], 0, 130, '…')) ?></p><div class="item-meta"><span>⌖ <?= e($item['location']) ?></span><span><?= e(format_date($item['event_date'])) ?></span></div><div class="item-card-bottom"><span>Posted by <?= e($item['poster_name']) ?></span><span class="status status-<?= e(strtolower($item['status'])) ?>"><?= e($item['status']) ?></span></div></article>
