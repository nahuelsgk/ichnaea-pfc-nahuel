[] Read Installation.md

[] Warning executing: app/console doctrine:generate:entities Ichnaea/WebApp/UserBundle/Entity/User
Must remove stubs created for the groups from the entity and clear cache

[] Clear cache
app/console clear:cache
Sometimes there is a warning. Just remove app/cache/dev/* app/cache/prod/*

[] Start enviroment
cd ../amqp && java -jar ./target/ichnaea-amqp.jar build-models:process -i ../r/files/ichnaea.sh --verbose
cd ../amqp && java -jar ./target/ichnaea-amqp.jar predict-models:process -i ../r/files/ichnaea.sh --verbose
app/console training:consumer
app/console prediction:consumer
