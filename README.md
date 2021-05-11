# faceleak
Script PHP requêtant une base de données PostgreSQL, avec interface web de recherche tournant sous caddy

Le docker-compose met en place:
- Une base de données postgre avec une base "faceleak", sans tables. Les identifiants par défaut sont "example" et "postgres"
- Une interface web de gestion "adminer" accessible sur le port 8080
- Un webserver caddy
- Et enfin un PHP

Le script contenu dans le répertoire HTML requête deux tables: "france" et "monde" qui seront a créer et dont les colonnes sont "phone", "id, "name" et "surname".
