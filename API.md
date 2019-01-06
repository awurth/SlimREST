# Routes

### `OPTIONS` [/{routes:.+}](http://localhost/slim-rest-base/{routes:.+})

### `POST` [/register](http://localhost/slim-rest-base/register)
##### controller.registration:register
###### register

### `POST` [/oauth/v2/token](http://localhost/slim-rest-base/oauth/v2/token)
##### controller.token:token
###### oauth_token

### `GET` [/user](http://localhost/slim-rest-base/user)
##### controller.token:user
###### user

### `GET` [/](http://localhost/slim-rest-base/)
##### controller.app:root
###### root

### `GET` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### controller.article:getArticle
###### get_article

### `GET` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### controller.article:getArticles
###### get_articles

### `POST` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### controller.article:postArticle
###### post_article

### `PUT` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### controller.article:putArticle
###### put_article

### `DELETE` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### controller.article:deleteArticle
###### delete_article

### `GET` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### controller.article.comment:getArticleComment
###### get_article_comment

### `GET` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### controller.article.comment:getArticleComments
###### get_article_comments

### `POST` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### controller.article.comment:postArticleComment
###### post_article_comment

### `PUT` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### controller.article.comment:putArticleComment
###### put_article_comment

### `DELETE` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### controller.article.comment:deleteArticleComment
###### delete_article_comment

