# create a docker image to test software against php5.3
FROM centos:6

MAINTAINER Solinor

RUN yum clean all
RUN yum update -y
RUN yum -y install php php-cli

