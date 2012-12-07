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

* install maven 3.0
    sudo apt-get install maven

* install eclipse
    sudo apt-get install eclipse

### Create an eclipse workspace
    
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
   
### Compile and Run

* compile the project jar
    mvn jar:jar
    
* run tests
    mvn test    

* run the project jar
	mvn exec:java -Dexec.mainClass="edu.upc.ichnaea.amqp.app.TestWorkApp"
	mvn exec:java -Dexec.mainClass="edu.upc.ichnaea.amqp.app.TestRequestApp"


