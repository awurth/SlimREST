# API Routes

### `POST` [/api/register](http://localhost/slim-rest-base/api/register)
##### AuthController:register
###### register

### `POST` [/api/login](http://localhost/slim-rest-base/api/login)
##### AuthController:login
###### login

### `GET` [/api/users/me](http://localhost/slim-rest-base/api/users/me)
##### AuthController:me
###### users.me

### `GET` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### ArticleController:getArticle
###### get_article

### `GET` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### ArticleController:getArticles
###### get_articles

### `POST` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### ArticleController:postArticle
###### post_article

### `PUT` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### ArticleController:putArticle
###### put_article

### `DELETE` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### ArticleController:deleteArticle
###### delete_article

### `GET` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### ArticleCommentController:getArticleComment
###### get_article_comment

### `GET` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### ArticleCommentController:getArticleComments
###### get_article_comments

### `POST` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### ArticleCommentController:postArticleComment
###### post_article_comment

### `PUT` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### ArticleCommentController:putArticleComment
###### put_article_comment

### `DELETE` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### ArticleCommentController:deleteArticleComment
###### delete_article_comment

