[] Warning executing: app/console doctrine:generate:entities Ichnaea/WebApp/UserBundle/Entity/User
Must remove stubs created for the groups from the entity and clear cache

[] Start enviroment
cd ../amqp && java -jar ./target/ichnaea-amqp.jar build-models:process -i ../r/files/ichnaea.sh --verbose
cd ../amqp && java -jar ./target/ichnaea-amqp.jar predict-models:process -i ../r/files/ichnaea.sh --verbose
app/console training:consumer
app/console prediction:consumer
