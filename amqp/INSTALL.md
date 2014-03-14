Installation instructions
=========================

On Ubuntu
---------

### For execution

TO execute the ichnaea amqp publishers and consumers you only need to build the
main jar once.

#### Setup ichnaea R scripts

* install R interpreter
    sudo apt-get install r-base-core
    
* install required R modules
    sudo ../r/files/ichnaea.sh --install --debug

* test ichnaea running it from the command line

##### Fake request

    ../r/files/ichnaea.sh --debug fake 10:1

##### Build models request

    ../r/files/ichnaea.sh --debug --aging ../r/fixtures/aging --models /tmp/cyprus_models.zip build ../r/fixtures/cyprus.csv

##### Predict models request

    ../r/files/ichnaea.sh --debug --models ../r/fixtures/cyprus_models.zip predict ../r/fixtures/cyprus_test.csv

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

This will build an executable file in `target/ichnaea-amqp.jar`.
Use the `-h` command line argument to get all the available options.

##### Fake request

To run the basic fake:process client:

    ./target/ichnaea-amqp.jar fake:process -i ../r/files/ichnaea.sh --verbose

To run the basic fake:request client:

    ./target/ichnaea-amqp.jar fake:request --duration=10 --interval=1

##### Build models request

To run the basic build-models:process client:

    ./target/ichnaea-amqp.jar build-models:process -i ../r/files/ichnaea.sh --verbose

To run the basic build-models:request client:

    ./target/ichnaea-amqp.jar build-models:request --aging=../r/fixtures/aging/env%column%-%aging%.txt --dataset=../r/fixtures/cyprus.csv --output=/tmp/cryprus_models.zip

##### Predict models request

To run the basic predict-models:process client:

    ./target/ichnaea-amqp.jar predict-models:process -i ../r/files/ichnaea.sh --verbose
    
To run the basic predict-models:request client with a real request:

    ./target/ichnaea-amqp.jar predict-models:request --data=../r/fixtures/cyprus_models.zip --dataset=../r/fixtures/cyprus_test.csv --output=/tmp/cryprus_result.txt
    
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
    mvn exec:java -Dexec.args="build-models:process -i ../r/files/ichnaea.sh"
    mvn exec:java -Dexec.args="build-models:process -i ../r/files/ichnaea.sh -s ssh://user:password@localhost"

    * issue a fake test build-models request
    mvn exec:java -Dexec.args="build-models:request --fake 10:1"

    * debug a build-models request
    mvn exec:java -Dexec.args="build-models:request --debug --dataset ../r/fixtures/cyprus.csv --aging ../r/fixtures/aging/env%column%-%aging%.txt"

### PHP setup

See `php/README.md`.

#### Possible problems

By default the server will [block connections](http://stackoverflow.com/questions/10427028/rabbitmq-connection-in-blocking-state)
if it does not have enough memory or disk space. To check if the requests are being connections instal the web managment.

    sudo rabbitmq-plugins enable rabbitmq_management

Restart the server for the changes to take effect

    sudo service rabbitmq-server restart

Open the web browser at http://localhost:55672 and enter with user `guest` password `guest`.

### Building documentation

The in-depth document can be found at doc/ichnaea_amqp.md and can be converted from markdown
to different formats using `pandoc`.

    sudo apt-get install pandoc latex-beamer

To convert to pdf use the following command:

    pandoc -s --highlight-style pygments -o ichnaea_amqp.pdf ichnaea_amqp.md

To convert to html use the following command:

    pandoc -s --highlight-style pygments -o ichnaea_amqp.html ichnaea_amqp.md