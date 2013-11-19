Installation instructions
=========================

On Ubuntu
---------

### For execution

TO execute the ichnaea amqp publishers and consumers you only need to build the
main jar once.

#### Setup required programms and libraries

* install oracle jdk 7
    sudo add-apt-repository ppa:webupd8team/java
    sudo apt-get update
    sudo apt-get install oracle-java7-installer

* install rabbitmq-server
    sudo apt-get install rabbitmq-server

* install maven 3.0
    sudo apt-get install maven

#### Compile and Run

Build a self contained jar file with all the dependencies

    mvn clean compile assembly:single

This will build a file in `target/ichnaea-amqp.jar`.

To run the basic build-models:process consumer:

    ./target/ichnaea-amqp.jar build-models:process -i ../kvm/files/ichnaea.sh

To run the basic build-models:request publisher with a fake request:

    ./target/ichnaea-amqp.jar build-models:request -f 10:1

To run the basic build-models:request publisher with a real request:

    ./target/ichnaea-amqp.jar build-models:request --aging=../kvm/fixtures/aging/env%column%-%aging%.txt --dataset=../kvm/fixtures/cyprus.csv

Use the `-h` command line argument to get all the available options.

### For Development

#### Setup required programms and libraries

* install oracle jdk 7
    sudo add-apt-repository ppa:webupd8team/java
    sudo apt-get update
    sudo apt-get install oracle-java8-installer

* install rabbitmq-server
    sudo apt-get install rabbitmq-server

* install maven 3.0
    sudo apt-get install maven

* install eclipse
    sudo apt-get install eclipse

* update eclipse
    sudo eclipse
    Help -> Check for updates

#### Create an eclipse workspace
    
* run eclipse and create a workspace
    eclipse
    
* install m2eclipse plugin
    http://download.eclipse.org/technology/m2e/releases
    
* add maven repo to eclipse
    mvn -Declipse.workspace=<path-to-eclipse-workspace> eclipse:configure-workspace

* create eclipse project file
    mvn eclipse:eclipse

* add the project to the workspace
    Import... -> Existing projects into Workspace
   
#### Compile and Run

* compile the project jar
    mvn jar:jar
    
* run tests
    mvn test    

* run the project jar

    * listen to new build-models requests
    mvn exec:java -Dexec.args="build-models:process -i ../kvm/files/ichnaea.sh"
    mvn exec:java -Dexec.args="build-models:process -i ../kvm/files/ichnaea.sh -s ssh://user:password@localhost"

    * issue a fake test build-models request
    mvn exec:java -Dexec.args="build-models:request --fake 10:1"

    * debug a build-models request
    mvn exec:java -Dexec.args="build-models:request --debug --dataset ../kvm/fixtures/cyprus.csv --aging ../kvm/fixtures/aging/env%column%-%aging%.txt"

### PHP setup

See `php/README.md`.


#### Possible problems

By default the server will [block connections](http://stackoverflow.com/questions/10427028/rabbitmq-connection-in-blocking-state)
if it does not have enough memory or disk space. To check if the requests are being connections instal the web managment.

    sudo rabbitmq-plugins enable rabbitmq_management

Restart the server for the changes to take effect

    sudo service rabbitmq-server restart

Open the web browser at http://localhost:55672 and enter with user guest password guest.


