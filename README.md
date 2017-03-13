# Rebilly Parody Application for Nutra

This application shows a sales page.

## Prerequisite
Download and install Vagrant here: https://www.vagrantup.com/downloads.html

Download and install VirtualBox here: https://www.virtualbox.org/wiki/Downloads

Install hostmanager plugin

    vagrant plugin install vagrant-hostmanager

## Run with Vagrant
Run this command from the root directory.

    vagrant up

## Install the Application
Run this command from the app root directory.

    composer install

## Setup Rebilly Config
Run this command from the app root directory.

    php public/index.php /setup GET

That's it!

Then access http://www.dev-local.upsell.com

