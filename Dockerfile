# create a docker image to test software against php 5.3
# Usage:
#   docker build --tag=solinor/phpunit:latest .
# run tests:
#   docker run --rm --workdir="/sdk" -it -v $PWD:/sdk solinor/phpunit:2 bin/phpunit

FROM centos:6

MAINTAINER Solinor

RUN yum install epel-release -y && yum clean all && yum update -y
RUN rpm -i https://dl.iuscommunity.org/pub/ius/stable/CentOS/6/x86_64/ius-release-1.0-14.ius.centos6.noarch.rpm
RUN yum -y install php54 php54-cli php54-xml

