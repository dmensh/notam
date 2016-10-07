## Installation

1. Clone the project

```
git clone https://github.com/dmensh/notam.git
```

2. Edit the `config.json` file to provide login credentials for the RocketRoute API.

3. I have created Vagrant configuration that allows automatic project deployment on a local machine. First install
[VirtualBox](https://www.virtualbox.org/wiki/Downloads) and [Vagrant](https://www.vagrantup.com), then execute
the following:

```
cd notam
vagrant up
```

On some terminals parts of the output may highlight in red, this is not an error.

A virtual machine will be created and configured with required dependencies, shared folder and port forwarding.
You can then access the project on [http://localhost:8080](http://localhost:8080)

## Running tests

First you need to SSH inside the virtual machine:

```
cd projectDir
vagrant ssh
```

Change the working directory to `/vagrant`

```
cd /vagrant
```

Unit tests with PHPUnit:

```
./vendor/bin/phpunit
```

Functional tests with CasperJs:

```
casperjs test tests/casperjs/functional.js
```

## Technologies used

- Silex - for backend API
- Bower - to install frontend dependencies
- RequireJs - for frontend dependency injection
- Gmaps.js - library for easier interaction with Google Maps API
- CasperJs - functional testing tool
- PhantomJs - as backend for CasperJs
- SweetAlert - for prettier alert overlays