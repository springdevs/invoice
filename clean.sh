composer install --no-dev --ignore-platform-reqs
rm -r composer.json
rm -r composer.lock

rm -r README.md

rm -rf .git/
rm -r .editorconfig
rm -r .gitignore
rm -r .phpcs.xml
rm -r .travis.yml

rm -rf cypress/
rm -r cypress.config.js
rm -r package.json
rm -r yarn.lock

echo "Production Ready 📦"
rm -r clean.sh
