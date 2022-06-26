# A Recipe organization API

## Development

Launching the Application:

``php -S 0.0.0.0:8000 -t public``

## Deployment 

linking the storage folders

``ln -s /home/michel/php-recipes/blog/storage/app/ /home/michel/php-recipes/blog/public/storage/``
``mv app img``

## The Data base

This API uses a standard MySQL db, check the .env file for access credentials.