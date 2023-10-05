dir=${CURDIR}
app_pool_cache_dir=$(dir)/var/app_pool_cache

pname=${COMPOSE_PROJECT_NAME}
project=-p $(pname)
symfony_user=-u www-data
openssl_bin:=$(shell which openssl)
interactive:=$(shell [ -t 0 ] && echo 1)
ifneq ($(interactive),1)
	optionT=-T
endif
ifeq ($(GITLAB_CI),1)
	# Determine additional params for phpunit in order to generate coverage badge on GitLabCI side
	phpunitOptions=--coverage-text --colors=never
endif

ifndef APP_ENV
	include .env
	# Determine if .env.local file exist
	ifneq ("$(wildcard .env.local)","")
		include .env.local
	endif
endif

exec:
	@docker-compose $(project) exec symfony $$cmd

ssh:
	@docker-compose $(project) exec $(optionT) $(symfony_user) symfony bash

migrate:
	@make exec cmd="php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing"

drop-migrate:
	@make exec cmd="php bin/console doctrine:schema:drop --full-database --force"
	@make migrate

fixtures:
	@make exec cmd="php bin/console doctrine:fixtures:load"

install:
	@make start
	@make exec cmd="composer install"
	@make migrate
	@make fixtures

start:
	@docker-compose up -d
stop:
	@docker-compose down
