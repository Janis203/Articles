<?php

namespace App\Controller;

use App\Model\Article;
use Exception;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ArticleController
{
    private Environment $twig;
    private LoggerInterface $logger;
    private Article $article;

    public function __construct(Environment $twig, LoggerInterface $logger, Article $article)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->article = $article;
    }

    public function list(): void
    {
        try {
            $articles = $this->article->getArticles();
            echo $this->twig->render('list.twig', ['articles' => $articles]);
            $this->logger->info('Displayed Article List.');
        } catch (Exception $e) {
            $this->logger->error('Error displaying Article List: ' . $e->getMessage());
            echo "Error displaying Article List";
        }
    }

    public function view(array $vars): void
    {
        try {
            $id = $vars['id'];
            $article = $this->article->getArticle($id);
            if ($article) {
                echo $this->twig->render('view.twig', ['article' => $article]);
                $this->logger->info('Displayed article with id: ' . $id);
            } else {
                $this->logger->warning('Article not found with ID: ' . $id);
                echo 'Article not found';
            }
        } catch (Exception $e) {
            $this->logger->error('Error displaying article: ' . $e->getMessage());
            echo 'Error displaying article';
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function create(): void
    {
        echo $this->twig->render('create.twig');
    }

    public function store(): void
    {
        try {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $this->article->create($title, $content);
            $this->logger->info('Created new article with title: ' . $title);
            header('Location: /');
        } catch (Exception $e) {
            $this->logger->error('Error creating article: ' . $e->getMessage());
            echo "Error creating article";
        }
    }

    public function edit(array $vars): void
    {
        try {
            $id = $vars['id'];
            $article = $this->article->getArticle($id);
            if ($article) {
                echo $this->twig->render('edit.twig', ['article' => $article]);
                $this->logger->info('Editing article with id: ' . $id);
            } else {
                $this->logger->warning('Article not found to edit with id: ' . $id);
                echo "Article not found";
            }
        } catch (Exception $e) {
            $this->logger->error('Error editing article: ' . $e->getMessage());
            echo "Error editing article";
        }
    }

    public function update(array $vars): void
    {
        try {
            $id = $vars['id'];
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $this->article->update($id, $title, $content);
            $this->logger->info('Updated Article with id:' . $id);
            header('Location: /article/' . $id);
        } catch (Exception $e) {
            $this->logger->error('Error updating article: ' . $e->getMessage());
            echo "Error updating article";
        }
    }

    public function delete(array $vars): void
    {
        try {
            $id = $vars['id'];
            $this->article->delete($id);
            header('Location: /');
        } catch (Exception $e) {
            $this->logger->error("Error deleting article: " . $e->getMessage());
            echo "Error deleting article";
        }
    }
}