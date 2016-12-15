etsy-redirect
=============

# Installation
This is a Symfony 3.2 project. You'll need to have [composer installed](https://getcomposer.org/download/)

```shell
$ composer install
$ ./bin/console doctrine:schema:update --force
$ ./bin/console server:run
```
The application is now available at http://localhost:8000

# Notes on the implementation
- This need a recent browser to work. JavaScript needs support for ES2015. CSS needs support for the latest flexbox specification. Although browser compatibility is important to me, I did not setup compatibility tools like Babel or Autoprefixr.
- I've only tested on devices I own (macOS, iOS) so some features might not work on other environments. I suspect the copy to clipboard functionality might be broken in some browsers.
- I did not split or factorise the code as well as theory would suggest. I believe you need a few examples to create a good abstraction. And an abstraction that's only used once ususally makes the code harder to read and evolve.
- I've used sqlite as a database here because there is no need to configure it on most systems. It is not suitable for production.
- I've chosen to use UUIDs for URL slugs. Compared to sequential ids, it prevents outsiders from having an estimate of the number of urls shortened, the rate at which urls are being shortened or to list a lot of them by following the sequence. They have the inconvenience of being pretty long though. For a real product, we might not want that and go with another strategy.
