
## FORK FROM https://github.com/devig/voyager-bread-generator with some modifications.

## Voyager BREAD generator

There is a common issue when we try to deploy local projects to a different environment. Currently, we need to export the database or so, in order to keep all the new BREADs structure across all the environments.

The only way to do that without having to create a database import each time is by creating the migrations, seeds, etc. for each bread.

This allows the developers to create new BREADs from the command line using Artisan.

## How to use:

### create a new bread
```bash
php artisan voyager:bread books
```

You can also generate the model and migration files

```bash
php artisan voyager:bread books --migration --model
```

### Configure the bread
This command will create a new BooksBreadSeeder file with the basic configuration for a new bread-seed, there you can add/edit all the bread fields. See DataRowsTableSeeder

Once the seeder is done you need to run:

```bash
php artisan db:seed --class=BooksBreadSeeder
```

Optionally you need to re-generate the permissions from the command line
```bash
php artisan db:seed --class=PermissionRoleTableSeeder
```
If you get error "class does not exist"

```bash
composer dump-autoload
```

You can also do this manually from the admin panel

Don't forget to run the new migration
```bash
php artisan migrate
```
