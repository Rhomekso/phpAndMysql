<?php
// controllers/ArticleController.php
// Controller handles request flow and calls the model; chooses the right view.

require_once __DIR__ . '/../models/Article.php';

class ArticleController
{
    private $articleModel;

    public function __construct()
    {
        $this->articleModel = new Article();

        // Start session for simple flash messaging
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // GET /?action=index
    public function index(): void
    {
        $articles = $this->articleModel->getAll();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']); // Consume flash

        require __DIR__ . '/../views/article/index.php';
    }

    // GET|POST /?action=create
    public function create(): void
    {
        $errors = [];
        $values = ['title' => '', 'content' => '', 'author' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title   = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $author  = trim($_POST['author'] ?? '');

            if ($title === '')   { $errors['title'] = 'Title is required.'; }
            if ($content === '') { $errors['content'] = 'Content is required.'; }
            if ($author === '')  { $errors['author'] = 'Author is required.'; }

            $values = ['title' => $title, 'content' => $content, 'author' => $author];

            if (empty($errors)) {
                $this->articleModel->create($title, $content, $author);
                $_SESSION['flash'] = 'Article created successfully.';
                header('Location: index.php?action=index');
                exit;
            }
        }

        require __DIR__ . '/../views/article/create.php';
    }

    // GET|POST /?action=edit&id=123
    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid article ID.';
            return;
        }

        $article = $this->articleModel->getById($id);
        if (!$article) {
            http_response_code(404);
            echo 'Article not found.';
            return;
        }

        $errors = [];
        $values = $article;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title   = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $author  = trim($_POST['author'] ?? '');

            if ($title === '')   { $errors['title'] = 'Title is required.'; }
            if ($content === '') { $errors['content'] = 'Content is required.'; }
            if ($author === '')  { $errors['author'] = 'Author is required.'; }

            $values = ['id' => $id, 'title' => $title, 'content' => $content, 'author' => $author];

            if (empty($errors)) {
                $updated = $this->articleModel->update($id, $title, $content, $author);
                if ($updated) {
                    $_SESSION['flash'] = 'Article updated successfully.';
                    header('Location: index.php?action=index');
                    exit;
                } else {
                    $errors['general'] = 'Update failed. Please try again.';
                }
            }
        }

        require __DIR__ . '/../views/article/edit.php';
    }

    // POST /?action=delete
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid article ID.';
            return;
        }

        $this->articleModel->delete($id);
        $_SESSION['flash'] = 'Article deleted.';
        header('Location: index.php?action=index');
        exit;
    }
}
