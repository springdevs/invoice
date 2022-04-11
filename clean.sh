composer dump-autoload
rm -r composer.json
rm -r composer.lock

rm -r README.md

rm -rf .git/
rm -r .editorconfig
rm -r .gitignore

echo "Production Ready 📦"
rm -r clean.sh
