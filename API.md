# API Routes

### `POST` /api/register
> AuthController:register

_register_

### `POST` /api/login
> AuthController:login

_login_

### `GET` /api/users/me
> AuthController:me

_users.me_

### `GET` /api/articles/{id:[0-9]+}
> ArticleController:getArticle

_get_article_

### `GET` /api/articles
> ArticleController:getArticles

_get_articles_

### `POST` /api/articles
> ArticleController:postArticle

_post_article_

### `PUT` /api/articles/{id:[0-9]+}
> ArticleController:putArticle

_put_article_

### `DELETE` /api/articles/{id:[0-9]+}
> ArticleController:deleteArticle

_delete_article_

### `GET` /api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}
> ArticleCommentController:getArticleComment

_get_article_comment_

### `GET` /api/articles/{article_id:[0-9]+}/comments
> ArticleCommentController:getArticleComments

_get_article_comments_

### `POST` /api/articles/{article_id:[0-9]+}/comments
> ArticleCommentController:postArticleComment

_post_article_comment_

### `PUT` /api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}
> ArticleCommentController:putArticleComment

_put_article_comment_

### `DELETE` /api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}
> ArticleCommentController:deleteArticleComment

_delete_article_comment_

