touch start.sh
echo "#!/bin/sh" > start.sh
echo "php artisan serve --host=0.0.0.0 --port=\${PORT:-8000}" >> start.sh
chmod +x start.sh
