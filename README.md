### Alias a hostname on Mac OSX

- sudo vim /etc/hosts
- add ( 127.0.0.1  crawl.local )


### Setup project

- git clone https://github.com/socloccoc/lancers.git
- cd app/crawl
- cp .env.example .env
- cd infra
- docker-compose up -d nginx mysql adminer workspace
- docker-compose exec --user=laradock workspace bash
- cd crawl
- cp .env.example .env
- composer install
- php artisan key:generate
- php artisan migrate
- php artisan optimize

### Crawl data from lancers page
- cd app/crawl
- php artisan lancers:crawl

### Crawl data from sokudan page
- cd app/crawl
- php artisan sokudan:crawl

### Crawl data from sokudan page
- cd app/crawl
- php artisan crowdworks:crawl

### Adminer

- http://crawl.local:8081

