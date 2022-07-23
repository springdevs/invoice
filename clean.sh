composer install --no-dev --ignore-platform-reqs
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
rm -r phpunit.xml
rm -r .phpcs.xml
rm -r .phpunit.result.cache
rm -r .travis.yml

echo "Production Ready 📦"
rm -r clean.sh
