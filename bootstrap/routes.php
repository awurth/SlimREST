<?php

use App\Service\RestRouter;

$dir = __DIR__ . '/../src/App/Resources/routes/';

$router = new RestRouter($container['router'], $settings['settings']['rest']);

/**
 *        URL          |           CONTROLLER            |     ROUTE
 * --------------------|---------------------------------|----------------
 * GET /articles       | ArticleController:getArticle    | get_articles
 * GET /articles/:id   | ArticleController:getArticles   | get_article
 * POST /articles      | ArticleController:postArticle   | post_article
 * PUT /articles/:id   | ArticleController:putArticle    | put_article
 * DELETE /article/:id | ArticleController:deleteArticle | delete_article
 */
// $router->crud('articles', 'ArticleController');

// OR

// $router->cget('articles', 'ArticleController');
// $router->get('articles', 'ArticleController');
// $router->post('articles', 'ArticleController');
// $router->put('articles', 'ArticleController');
// $router->delete('articles', 'ArticleController');
