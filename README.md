# LaCagna


**LaCagna**: a Zend Framework application.

**How to choose your drink without paper menu ?**



This little application uses many modules:

- view/templates: zfc-twig
- database ORM: Doctrine
- user: zfc-user + zfc-user-doctrine-orm + bjyoungblood/bjy-authorize
- categories: cbsi/doctrine2nestedset
- images: Imagine/Imagick
- DomPDF: dino/dompdf-module




We use [ZendSkeletonApplication] as default layout/CSS,
It should be "mobile first".

## Objectives

- [x] Products listing
- [x] Categories management (tree)
- [x] Simple images fetch (thumbs generation)
- [x] User roles
- [x] Dynamic navigation
- [ ] Articles/characteristics/prices management
- [ ] Generate ready to print menu

## Installation

### Clone project

```sh
git clone git@github.com:Jodaille/LaCagna.git
```

It will create a directory **LaCagna**

Go inside it:

```sh
cd LaCagna
```

### Install dependencies

```sh
php composer.phar update
```

Nb: use composer.json

> It could take some time to get ZendFramework and so on

### Create a database

You will find a simple SQL file: [DbSample]

That will create a database named **lacagna**,

with a user **valtom** having a password **CHANGEME**

You will find details in docs directory [InstallNotes]

### Database configuration

rename or duplicate the file
**doctrine.local.php.dist.dist** to **doctrine.local.php**

in **config/[autoload]** directory

Then edit values for database access: dbname, user, password ...

Update database schema:
```sh
php public/index.php  orm:schema-tool:update --force
```

Database will be updated from [Entities].

Then you can add roles using [RoleEntries] SQL file.

### Virtual Host configuration

You can find a basic configuration file for Apache web server: [VHost]

### User creation and administrator role

Now database has its structure but is still empty.

Now we need to create a user:
http://lacagna.local/user/register


In order to see administration pages,
you have to add your user id in table **user_role_linker**,
and the id of the administrator role (4)

For example for user with id = 1 :
```sql
INSERT INTO `user_role_linker` (`user_id`, `role_id`) VALUES
(1, 4);
```
## Add entries in menu

```sql
INSERT INTO `menu` (`id`, `label`, `route`, `displayorder`, `role`) VALUES
(1, 'BO', 'adminindex', 50, 4),
(2, 'Produits', 'adminproductslist', 10, 1),
(3, 'Catégories', 'admincategorieslist', 15, 1);
```
## Create first category

Categories use Nested set model.

To create a root category named "Accueil",
in your terminal in your project directory:

```sql
php public/index.php create root Accueil
```

## Writables directories

some directories needs to be writable by apache.

- [ ] **data**
- [ ] **public/img**

**data** for cache and images upload.

**public/img** for thumbnails of images.

[ChangeRights]:https://github.com/Jodaille/LaCagna/tree/master/home/jody/LaCagna/ChangeRights.sh
[autoload]:https://github.com/Jodaille/LaCagna/tree/master/config/autoload
[Entities]:https://github.com/Jodaille/LaCagna/tree/master/module/LaCagnaProduct/src/LaCagnaProduct/Entity
[DbSample]:https://github.com/Jodaille/LaCagna/blob/master/docs/create_database_sample.sql
[RoleEntries]:https://github.com/Jodaille/LaCagna/blob/master/docs/add_role_entries.sql
[VHost]:https://github.com/Jodaille/LaCagna/blob/master/docs/vhost_sample_config
[InstallNotes]:https://github.com/Jodaille/LaCagna/blob/master/docs/README.md
[ZendSkeletonApplication]:https://github.com/zendframework/ZendSkeletonApplication
