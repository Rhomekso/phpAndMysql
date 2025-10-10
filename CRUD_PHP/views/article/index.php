<?php
// views/article/index.php
// List all articles with actions

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
function truncate($text, $limit = 240) {
    $text = (string)$text;
    if (function_exists('mb_strlen')) {
        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit) . '…' : $text;
    }
    return strlen($text) > $limit ? substr($text, 0, $limit) . '…' : $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Articles - CRUD App</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root { --bg:#f8fafc; --card:#ffffff; --text:#0f172a; --muted:#64748b; --primary:#2563eb; --danger:#dc2626; --success:#16a34a; --border:#e2e8f0; }
* { box-sizing: border-box; }
body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji"; background:var(--bg); color:var(--text); }
.header { background:#0ea5e9; color:white; padding:16px 24px; }
.container { max-width: 900px; margin: 24px auto; padding: 0 16px; }
.actions { display:flex; justify-content: space-between; align-items:center; margin-bottom:16px; }
.btn { display:inline-block; padding:10px 14px; border-radius:8px; text-decoration:none; border:1px solid transparent; font-weight:600; cursor:pointer; }
.btn-primary { background:var(--primary); color:white; }
.btn-outline { background:white; color:var(--primary); border-color:var(--primary); }
.btn-danger { background:var(--danger); color:white; }
.btn-sm { padding:6px 10px; font-size: 0.9rem; }
.card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:12px; }
.meta { color:var(--muted); font-size:0.9rem; margin-top:6px; }
.flash { background:#dcfce7; border:1px solid #86efac; color:#14532d; padding:10px 12px; border-radius:8px; margin:16px 0; }
.row-actions { margin-top:12px; display:flex; gap:8px; }
.empty { color:var(--muted); padding:16px; text-align:center; }
.title { margin:0; }
.content-preview { margin:8px 0 0; white-space: pre-line; }
</style>
</head>
<body>
<div class="header">
  <h1>Articles</h1>
</div>
<div class="container">

  <div class="actions">
    <p class="muted">A simple PHP + MySQL CRUD using MVC.</p>
    <a class="btn btn-primary" href="index.php?action=create">New Article</a>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="flash"><?= e($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($articles)): ?>
    <div class="card empty">No articles found. Create the first one!</div>
  <?php else: ?>
    <?php foreach ($articles as $a): ?>
      <div class="card">
        <h2 class="title"><?= e($a['title']) ?></h2>
        <p class="meta">
          By <?= e($a['author']) ?> · Created <?= e($a['created_at']) ?> · Updated <?= e($a['updated_at']) ?>
        </p>
        <p class="content-preview"><?= e(truncate($a['content'])) ?></p>
        <div class="row-actions">
          <a class="btn btn-outline btn-sm" href="index.php?action=edit&amp;id=<?= (int)$a['id'] ?>">Edit</a>
          <form method="post" action="index.php?action=delete" onsubmit="return confirm('Delete this article?');" style="display:inline;">
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
</body>
</html>
