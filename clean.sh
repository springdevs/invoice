composer install --no-dev
rm -r composer.json
rm -r composer.lock

rm -r README.md

rm -rf .git/
rm -r .editorconfig
rm -r .gitignore

rm -rf tests/
rm -rf mysql/
rm -rf bin/
rm -r docker-compose.yml
rm -r phpunit.yml
rm -r .phpcs.yml
rm -r .phpunit.result.cache
rm -r .travis.yml

echo "Production Ready 📦"
rm -r clean.sh
