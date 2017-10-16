<?php

use App\Core\Rest\Router as RestRouter;

$router = new RestRouter($container['router'], $container['config']['rest']);

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
    $this->post('/register', 'security.auth.controller:register')->setName('register');
    $this->post('/login', 'security.auth.controller:login')->setName('login');
    $this->post('/auth/refresh', 'security.auth.controller:refresh')->setName('jwt.refresh');
    $this->get('/users/me', 'security.auth.controller:me')
        ->add($container['auth.middleware']())
        ->setName('users.me');
});

$app->get('/', 'core.controller:root')->setName('root');

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

// With options
/**
 * $options = [
 *      'key' => 'id',
 *      'requirement' => '[0-9]+',
 *      'singular' => 'article'
 * ];
 *
 * $router->CRUD('articles', 'ArticleController', [], $options);
 *
 * OR
 *
 * $router->get('articles', 'ArticleController', $options);
 * ...
 */

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

// With options
/**
 * $options = [
 *      'parent_key' => 'article_id',
 *      'parent_requirement' => '[0-9]+',
 *      'sub_key' => 'comment_id',
 *      'sub_requirement' => '[0-9]+',
 *      'parent_singular' => 'article',
 *      'sub_singular' => 'comment'
 * ];
 *
 * $router->subCRUD('articles', 'comments', 'ArticleCommentController', [], $options);
 *
 * OR
 *
 * $router->getSub('articles', 'comments', 'ArticleController', $options);
 * ...
 */
