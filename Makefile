.PHONY: help
.DEFAULT_GOAL := help

$(VERBOSE).SILENT:

help:
    grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
    cut -d: -f2- | \
    sort -d | \
    awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-16s\033[0m %s\n", $$1, $$2}'

.PHONY: up down composer-install composer-update test cs cs-fix

up:
	docker-compose -f docker-compose.examples.yml stop --timeout=2 && docker-compose -f docker-compose.examples.yml up -d

down:
	docker-compose -f docker-compose.examples.yml stop --timeout=2

composer-install:
	docker run --rm --interactive --tty --volume $(PWD):/app --user $(id -u):$(id -g) composer install --ignore-platform-reqs

composer-update:
	docker run --rm --interactive --tty --volume $(PWD):/app --user $(id -u):$(id -g) composer update --ignore-platform-reqs

test:
	docker-compose -f docker-compose.yml up

cs:
	docker-compose -f docker-compose.yml run larva-php /code/vendor/bin/phpcs \
	    --standard=/code/phpcs.xml \
	    --encoding=UTF-8 \
	    --report-full \
	    --extensions=php \
	   /code/src/ /code/tests/

cs-fix:
	docker-compose -f docker-compose.yml run larva-php /code/vendor/bin/phpcbf \
	    --standard=/code/phpcs.xml \
	    --extensions=php \
	   /code/src/ /code/tests/
