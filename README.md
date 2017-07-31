# TechPromux Dynamic Report Bundle: for dynamic reports from databases

This project is a symfony based bundle with possibility to create/execute multiple dynamic reports from databases.

It provides a custom wizard for create multiple reports trought select queries, widgets and some custom configurations. 

You can add/construct multiple configurations for many types of widgets like tables, charts, etc.  

You only need download it and use it with a little effort. 

We hope that this project contribute to your work with Symfony.

# Instalation

Open a console in root project folder and execute following command:

    composer install techpromux/dynamic-report-bundle

# Configuration

For custom database and other options edit files:

	// TODO

Create/Update tables from entities definitions, executing following command:

    ./bin/console doctrine:schema:update --force


Force bundle to copy all assets in public folder, executing following command:

    ./bin/console assets:install web (for Symfony <= 3.3)

    ./bin/console assets:install public (for Symfony >= 3.4)
