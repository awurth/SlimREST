<?php

use App\Rest\Router as RestRouter;

$router = new RestRouter($container['router'], $container['settings']['rest']);

/**
 * CORS Pre-flight request.
 */
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

/**
 * Security.
 */
$app->group('/', function () use ($container) {
    $this->post('register', 'controller.registration:register')->setName('register');
    $this->post('oauth/v2/token', 'controller.token:token')->setName('oauth_token');
    $this->get('user', 'controller.token:user')
        ->add($container['middleware.auth'])
        ->setName('user');
});

$app->get('/', 'controller.app:root')->setName('root');

/**
 *         URL          |           CONTROLLER            |     ROUTE
 * ---------------------|---------------------------------|----------------
 * GET /articles        | controller.article:getArticle    | get_articles
 * GET /articles/{id}   | controller.article:getArticles   | get_article
 * POST /articles       | controller.article:postArticle   | post_article
 * PUT /articles/{id}   | controller.article:putArticle    | put_article
 * DELETE /article/{id} | controller.article:deleteArticle | delete_article
 */
$router->crud('articles', 'controller.article');

// OR

/**
 * $router->cget('articles', 'controller.article');
 * $router->get('articles', 'controller.article');
 * $router->post('articles', 'controller.article');
 * $router->put('articles', 'controller.article');
 * $router->delete('articles', 'controller.article');
 */

// With options
/**
 * $options = [
 *      'key' => 'id',
 *      'requirement' => '[0-9]+',
 *      'singular' => 'article'
 * ];
 *
 * $router->crud('articles', 'controller.article', [], $options);
 *
 * OR
 *
 * $router->get('articles', 'controller.article', $options);
 * ...
 */

/***********************************************************/
/* -------------------- SUB RESOURCES -------------------- */
/***********************************************************/

/**
 *                        URL                         |                   CONTROLLER                  |        ROUTE
 * ---------------------------------------------------|-----------------------------------------------|------------------------
 * GET /articles/{article_id}/comments                | controller.article.comment:getArticleComments   | get_article_comments
 * GET /articles/{article_id}/comments/{comment_id}   | controller.article.comment:getArticleComment    | get_article_comment
 * POST /articles/{article_id}/comments               | controller.article.comment:postArticleComment   | post_article_comment
 * PUT /articles/{article_id}/comments/{comment_id}   | controller.article.comment:putArticleComment    | put_article_comment
 * DELETE /article/{article_id}/comments/{comment_id} | controller.article.comment:deleteArticleComment | delete_article_comment
 */
$router->subCrud('articles', 'comments', 'controller.article.comment');

// OR

/**
 * $router->cgetSub('articles', 'comments', 'controller.article');
 * $router->getSub('articles', 'comments', 'controller.article');
 * $router->postSub('articles', 'comments', 'controller.article');
 * $router->putSub('articles', 'comments', 'controller.article');
 * $router->deleteSub('articles', 'comments', 'controller.article');
 */

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
 * $router->subCrud('articles', 'comments', 'controller.article.comment', [], $options);
 *
 * OR
 *
 * $router->getSub('articles', 'comments', 'controller.article', $options);
 * ...
 */
