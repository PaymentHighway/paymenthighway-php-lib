# create a docker image to test software against php 5.3
# Usage:
#   docker build --tag=solinor/phpunit:latest .
# run tests:
#   docker run --rm --workdir="/sdk" -it -v $PWD:/sdk solinor/phpunit:2 bin/phpunit

FROM centos:6

MAINTAINER Solinor

RUN yum clean all
RUN yum update -y
RUN yum -y install php php-cli php-xml

