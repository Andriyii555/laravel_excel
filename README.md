1) clone project ``git clone https://github.com/Andriyii555/laravel_excel.git``
2) use master branch: ``git checkout master``
2) create mysql dstsbase and set connect config to '.env' file
3) run command ``composer install``
4) Instal all tables in DB. Run command: ``php artisan migrate``

ps: If U have got "RuntimeException No application encryption key has been specified." - run command ``php artisan key:generate``
It will generate Application Key for your application. You can find the generated application key(APP_KEY) in .env file.
