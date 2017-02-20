<?php

use App\Service\RestRouter;

$router = new RestRouter($container['router'], $config['rest']);

/**
 * CORS Pre-flight request
 */
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

/**
 * Authentication
 */
$app->group('/api', function () use ($container) {
    $this->post('/register', 'AuthController:register')->setName('register');
    $this->post('/login', 'AuthController:login')->setName('login');
    $this->get('/users/me', 'AuthController:me')
        ->add(new App\Middleware\AuthMiddleware($container))
        ->setName('users.me');
});

/**
 *         URL          |           CONTROLLER            |     ROUTE
 * ---------------------|---------------------------------|----------------
 * GET /articles        | ArticleController:getArticle    | get_articles
 * GET /articles/{id}   | ArticleController:getArticles   | get_article
 * POST /articles       | ArticleController:postArticle   | post_article
 * PUT /articles/{id}   | ArticleController:putArticle    | put_article
 * DELETE /article/{id} | ArticleController:deleteArticle | delete_article
 */
$router->CRUD('articles', 'ArticleController');

// OR

// $router->cget('articles', 'ArticleController');
// $router->get('articles', 'ArticleController');
// $router->post('articles', 'ArticleController');
// $router->put('articles', 'ArticleController');
// $router->delete('articles', 'ArticleController');

/***********************************************************/
/* -------------------- SUB RESOURCES -------------------- */
/***********************************************************/

/**
 *                        URL                         |                   CONTROLLER                  |        ROUTE
 * ---------------------------------------------------|-----------------------------------------------|------------------------
 * GET /articles/{article_id}/comments                | ArticleCommentController:getArticleComments   | get_article_comments
 * GET /articles/{article_id}/comments/{comment_id}   | ArticleCommentController:getArticleComment    | get_article_comment
 * POST /articles/{article_id}/comments               | ArticleCommentController:postArticleComment   | post_article_comment
 * PUT /articles/{article_id}/comments/{comment_id}   | ArticleCommentController:putArticleComment    | put_article_comment
 * DELETE /article/{article_id}/comments/{comment_id} | ArticleCommentController:deleteArticleComment | delete_article_comment
 */
$router->subCRUD('articles', 'comments', 'ArticleCommentController');

// OR

// $router->cgetSub('articles', 'comments', 'ArticleController');
// $router->getSub('articles', 'comments', 'ArticleController');
// $router->postSub('articles', 'comments', 'ArticleController');
// $router->putSub('articles', 'comments', 'ArticleController');
// $router->deleteSub('articles', 'comments', 'ArticleController');
