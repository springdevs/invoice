rm -rf vendor/
composer install --no-dev --ignore-platform-reqs
rm -r composer.json
rm -r composer.lock

rm -r README.md

rm -rf .git/
rm -r .editorconfig
rm -r .gitignore
rm -r phpcs.xml

echo "Production Ready 📦"
rm -r clean.sh
