start:
	symfony server:start --no-tls
live:
	npm run dev-server
watch:
	npm run watch
build:
	npm run build
clear:
	php bin/console cache:clear
