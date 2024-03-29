IMAGE_PHPTEST = fastwf/php-test

test-prepare: docker/test/Dockerfile
	docker build -t "$(IMAGE_PHPTEST)" docker/test

test:
	docker run -v $(PWD):$(PWD) -w $(PWD) --rm "$(IMAGE_PHPTEST):latest" ./vendor/bin/phpunit tests \
		--coverage-html build/cov-html 2>/dev/null
