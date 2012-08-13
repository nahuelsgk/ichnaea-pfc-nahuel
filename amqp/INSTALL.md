Installation instructions
=========================

On Ubuntu
---------

### Setup required programms and libraries

* install oracle jdk 7
    sudo add-apt-repository ppa:webupd8team/java
    sudo apt-get update
    sudo apt-get install oracle-jdk7-installer

* install rabbitmq-server
    sudo apt-get install rabbitmq-server

* install maven2
    sudo apt-get install maven2

* install eclipse
    sudo apt-get install eclipse
    
### Set up maven dependencies

* install maven dependencies
    mvn install
    
* add maven repo to class path

### Create an eclipse workspace
    
* run eclipse and create a workspace
    eclipse

* install m2eclipse plugin
    http://download.eclipse.org/technology/m2e/releases
    
* add the project to the workspace

* add maven repo to eclipse
    mvn -Declipse.workspace=<path-to-eclipse-workspace> eclipse:add-maven-repo

* update eclipse
    mvn eclipse:eclipse
   
### Compile and Run

* compile de project jar
    mvn jar:jar

* run the project jar
	mvn exec:java -Dexec.mainClass="edu.upc.ichnaea.amqp.app.TestConsumer"
	mvn exec:java -Dexec.mainClass="edu.upc.ichnaea.amqp.app.TestPublisher"


