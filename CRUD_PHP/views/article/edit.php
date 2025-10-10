<?php
// views/article/edit.php
// Form to edit an existing article

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
$values = $values ?? $article ?? [];
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Article - CRUD App</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root { --bg:#f8fafc; --card:#ffffff; --text:#0f172a; --muted:#64748b; --primary:#2563eb; --danger:#dc2626; --border:#e2e8f0; }
* { box-sizing: border-box; }
body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial; background:var(--bg); color:var(--text); }
.header { background:#0ea5e9; color:white; padding:16px 24px; }
.container { max-width: 760px; margin: 24px auto; padding: 0 16px; }
.card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:16px; }
label { display:block; margin-top:12px; font-weight:600; }
input[type="text"], textarea { width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-size:1rem; }
textarea { min-height: 160px; resize: vertical; }
.error { color: var(--danger); font-size: 0.9rem; margin-top:6px; }
.row { display:flex; gap:8px; margin-top:16px; }
.btn { display:inline-block; padding:10px 14px; border-radius:8px; text-decoration:none; border:1px solid transparent; font-weight:600; cursor:pointer; }
.btn-primary { background:var(--primary); color:white; }
.btn-outline { background:white; color:var(--primary); border-color:var(--primary); }
</style>
</head>
<body>
<div class="header">
  <h1>Edit Article</h1>
</div>
<div class="container">
  <div class="card">
    <?php if (!empty($errors['general'])): ?>
      <div class="error"><?= e($errors['general']) ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?action=edit&amp;id=<?= (int)($values['id'] ?? $article['id']) ?>" novalidate>
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= e($values['title'] ?? '') ?>" required>
      <?php if (!empty($errors['title'])): ?><div class="error"><?= e($errors['title']) ?></div><?php endif; ?>

      <label for="content">Content</label>
      <textarea id="content" name="content" required><?= e($values['content'] ?? '') ?></textarea>
      <?php if (!empty($errors['content'])): ?><div class="error"><?= e($errors['content']) ?></div><?php endif; ?>

      <label for="author">Author</label>
      <input type="text" id="author" name="author" value="<?= e($values['author'] ?? '') ?>" required>
      <?php if (!empty($errors['author'])): ?><div class="error"><?= e($errors['author']) ?></div><?php endif; ?>

      <div class="row">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a class="btn btn-outline" href="index.php?action=index">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
