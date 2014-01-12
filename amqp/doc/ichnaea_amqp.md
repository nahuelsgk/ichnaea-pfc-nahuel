# Ichnaea AMQP

## Introduction

### Ichnaea

Ichnaea was originally developed as a set of R language scripts to predict the origin of biological samples.

### AMQP

The Advanced Message Queuing Protocol (AMQP) is an open standard application layer protocol for message-oriented middleware. The defining features of AMQP are:

* message orientation
* queuing
* routing (including point-to-point and publish-and-subscribe)
* reliability
* security

AMQP mandates the behaviour of the messaging provider and client to the extent that implementations from different vendors are truly interoperable, in the same way as SMTP, HTTP, FTP, etc. have created interoperable systems. Previous attempts to standardize middleware have happened at the API level (e.g. JMS) and thus did not ensure interoperability. Unlike JMS, which merely defines an API, AMQP is a wire-level protocol. A wire-level protocol is a description of the format of the data that is sent across the network as a stream of octets. Consequently any tool that can create and interpret messages that conform to this data format can interoperate with any other compliant tool irrespective of implementation language.

AMQP is a binary protocol, designed to efficiently support a wide variety of messaging applications and communication patterns. It provides flow controlled, message-oriented communication with message-delivery guarantees such as at-most-once (where each message is delivered once or never), at-least-once (where each message is certain to be delivered, but may do so multiple times) and exactly-once (where the message will always certainly arrive and do so only once), and authentication and/or encryption based on SASL and/or TLS. It assumes an underlying reliable transport layer protocol such as Transmission Control Protocol (TCP).

The AMQP specification is defined in several layers:

* a type system
* a symmetric, asynchronous protocol for the transfer of messages from one process to another
* a standard, extensible message format
* a set of standardised but extensible messaging capabilities

#### History

AMQP was originated in 2003 by John O'Hara at JPMorgan Chase in London, UK. From the beginning AMQP was conceived as a co-operative open effort. Initial development was by JPMorgan Chase. from mid-2004 to mid-2006 who contracted iMatix Corporation for a C broker and protocol documentation. In 2005 JPMorgan Chase approached other firms to form a working group that included Cisco Systems, IONA Technologies, iMatix, Red Hat, and Transaction Workflow Innovation Standards Team (TWIST). In the same year JPMorgan Chase partnered with Red Hat to create Apache Qpid, initially in Java and soon after C++. Independently, RabbitMQ was developed in Erlang by Rabbit Technologies, followed later by the Microsoft and StormMQ implementations.

The working group grew to 23 companies including Bank of America, Barclays, Cisco Systems, Credit Suisse, Deutsche Börse Systems, Goldman Sachs, HCL Technologies Ltd, Progress Software, IIT Software, INETCO Systems Limited, Informatica Corporation (including 29 West), JPMorgan Chase, Microsoft Corporation, my-Channels, Novell, Red Hat, Software AG, Solace Systems, StormMQ, Tervela Inc., TWIST Process Innovations ltd, VMware (which acquired Rabbit Technologies) and WSO2.

In August 2011, the AMQP working group announced its reorganization into an OASIS member section.

AMQP 1.0 was released by the AMQP working group on 30 October 2011, at a conference in New York. At the event Microsoft, Red Hat, VMware, Apache, INETCO and IIT Software demonstrated software running the protocol in an interoperability demonstration. The next day, on 1 November 2011, the formation of an OASIS Technical Committee was announced to advance this contributed AMQP version 1.0 through the international open standards process. The first draft from OASIS was released in February 2012, the changes as compared to that published by the Working Group being restricted to edits for improved clarity (no functional changes). The second draft was released for public review on 20 June (again with no functional changes) and AMQP was approved as an OASIS standard on the 31st October, 2012.

Previous versions of AMQP were 0-8, published in June 2006, 0-9, published in December 2006, 0-10 published in February 2008 and 0-9-1, published in November 2008. These earlier releases are significantly different from the final 1.0 specification that emerged. However existing implementations may continue to support these earlier versions alongside 1.0.

Whilst AMQP originated in the financial services industry, it has general applicability to a broad range of middleware problems.

#### Type system

AMQP defines a self-describing encoding scheme allowing interoperable representation of a wide range of commonly used types. It also allows typed data to be annotated with additional meaning. The example given in the specification is indicating a particular string value is in fact to be understood as a URL. Likewise a map value containing key-value pairs for 'name', 'address' etc., might be annotated as being of representation of a 'customer' type.

The type-system is used to define a message format allowing standard and extended meta-data to be expressed and understood by processing entities. It is also used to define the communication primitives through which messages are exchanged between such entities, i.e. the AMQP frame bodies.

#### AMQP performatives and the link protocol

The basic unit of data in AMQP is a frame. There are nine AMQP frame bodies defined that are used to initiate, control and tear down the transfer of messages between two peers. These are:

* `open`
* `begin`
* `attach`
* `transfer`
* `flow`
* `disposition`
* `detach`
* `end`
* `close`

The link protocol is at the heart of AMQP. An attach frame body is sent to initiate a new link; a detach to tear down a link. Links may be established in order to receive or send messages.

Messages are sent over an established link using the transfer frame. Messages on a link flow in only one direction.
Transfers are subject to a credit based flow control scheme, managed using flow frames. This allows a process to protect itself from being overwhelmed by too large a volume of messages or more simply to allow a subscribing link to pull messages as and when desired.

Each transferred message must eventually be settled. Settlement ensures that the sender and receiver agree on the state of the transfer, providing reliability guarantees. Changes in state and settlement for a transfer (or set of transfers) are communicated between the peers using the disposition frame. Various reliability guarantees can be enforced this way: at-most-once, at-least-once and exactly-once.

Multiple links, in both directions, can be grouped together in a session. A session is a bidirectional, sequential conversation between two peers that is initiated with a begin frame and terminated with an end frame. A connection between two peers can have multiple sessions multiplexed over it, each logically independent. Connections are initiated with an open frame in which the sending peer's capabilities are expressed, and terminated with a close frame.

#### Message format

AMQP defines as the bare message, that part of the message that is created by the sending application. This is considered immutable as the message is transferred between one or more processes.

Ensuring the message as sent by the application is immutable allows for end-to-end message signing and/or encryption and ensures that any integrity checks (e.g. hashes or digests) remain valid. The message can be annotated by intermediaries during transit, but any such annotations are kept distinct from the immutable bare message. Annotations may be added before or after the bare message.

The header is a standard set of delivery related annotations that can be requested or indicated for a message and includes time to live, durability, priority.

The bare message itself is structured as an optional list of standard properties (message id, user id, creation time, reply to, subject, correlation id, group id etc.), an optional list of application-specific properties (i.e. extended properties) and a body (which AMQP refers to as application data).

Properties are specified in the AMQP type system, as are annotations. The application data can be of any form, and in any encoding the application chooses. One option is to use the AMQP type system to send structured, self-describing data.

#### Messaging capabilities

The link protocol transfers messages between two nodes but assumes very little as to what those nodes are or how they are implemented.
A key category is those nodes used as a rendezvous point between senders and receivers of messages (e.g. queues or topics). The AMQP specification calls such nodes distribution nodes and codifies some common behaviours.

This includes some standard outcomes for transfers, through which receivers of messages can for example accept or reject messages
a mechanism for indicating or requesting one of the two basic distribution patterns, competing- and non-competing- consumers, through the distribution modes move and copy respectively the ability to create nodes on-demand, e.g. for temporary response queues
the ability to refine the set of message of interest to a receiver through filters
Though AMQP can be used in simple peer-to-peer systems, defining this framework for messaging capabilities additionally enables interoperability with messaging intermediaries (brokers, bridges etc.) in larger, richer messaging networks. The framework specified covers basic behaviours but allows for extensions to evolve that can be further codified and standardised.

### RabbitMQ

RabbitMQ is an open-source implementation of a AMQP 1.0 compliant message broker written in the Erlang language built on the Open Telecom Platform framework for clustering and failover.

Rabbit Technologies Ltd., develops and provides support for RabbitMQ. Rabbit Technologies started as a joint venture between LShift and CohesiveFT in 2007, and was acquired in April 2010 by SpringSource, a division of VMware. The project became part of GoPivotal in May 2013.

In addition to the AMQP server, RabbitMQ also offers client libraries in multiple languages including Java.

## Implementation

### R wrapper

### Java RabbitMQ Consumer

### PHP library