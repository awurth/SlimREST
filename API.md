# API Routes

### `OPTIONS` [/{routes:.+}](http://localhost/slim-rest-base/{routes:.+})

### `POST` [/register](http://localhost/slim-rest-base/register)
##### registration.controller:register
###### register

### `POST` [/oauth/v2/token](http://localhost/slim-rest-base/oauth/v2/token)
##### token.controller:token
###### oauth_token

### `GET` [/user](http://localhost/slim-rest-base/user)
##### token.controller:user
###### user

### `GET` [/](http://localhost/slim-rest-base/)
##### app.controller:root
###### root

### `GET` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### article.controller:getArticle
###### get_article

### `GET` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### article.controller:getArticles
###### get_articles

### `POST` [/api/articles](http://localhost/slim-rest-base/api/articles)
##### article.controller:postArticle
###### post_article

### `PUT` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### article.controller:putArticle
###### put_article

### `DELETE` [/api/articles/{id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{id:[0-9]+})
##### article.controller:deleteArticle
###### delete_article

### `GET` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### article.comment.controller:getArticleComment
###### get_article_comment

### `GET` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### article.comment.controller:getArticleComments
###### get_article_comments

### `POST` [/api/articles/{article_id:[0-9]+}/comments](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments)
##### article.comment.controller:postArticleComment
###### post_article_comment

### `PUT` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### article.comment.controller:putArticleComment
###### put_article_comment

### `DELETE` [/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+}](http://localhost/slim-rest-base/api/articles/{article_id:[0-9]+}/comments/{comment_id:[0-9]+})
##### article.comment.controller:deleteArticleComment
###### delete_article_comment

