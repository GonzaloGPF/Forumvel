# Forumvel [![Build Status](https://travis-ci.org/GonzaloGPF/Forumvel.svg?branch=master)](https://travis-ci.org/GonzaloGPF/Forumvel)

A Forum created with Laravel and VueJS

## Installation

### Step 1

> To run this project, you must have PHP 7 installed as a prerequisite.

Begin by cloning this repository to your machine, and installing all Composer & NPM dependencies.

```bash
git clone git@github.com:GonzaloGPF/Forumvel.git
cd Forumvel && composer install && npm install
php artisan forum:install
npm run dev
```

### Step 2

Next, boot up a server and visit your forum. If using a tool like Laravel Valet, of course the URL will default to `http://council.test`. 

1. Visit: `http://council.test/register` to register a new forum account.
2. Edit `config/council.php`, and add any email address that should be marked as an administrator.
3. Visit: `http://council.test/admin/channels` to seed your forum with one or more channels.
