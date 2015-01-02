# Lemur

If you have to review pull/merge requests in multiple repositories along 
several platforms such as [Github](https://github.com) or 
[Gitlab](https://about.gitlab.com), you already know what a pain is to track
which ones you have already reviewed, which ones are waiting to be merged for too long, etc.

Lemur is a simple app which provides an unified view of all pull/merge requests
waiting to be reviewed or merged. To do that, it just needs to be connected to the
source repositories using the [webhooks](https://developer.github.com/webhooks/)
provided by them.

## Features

* See pull/merge requests from multiple repositories and services from a single place.
* Show +1/-1 comments in each pull request (Github only).
* Show pull requests' waiting time.

## Requirements

* [Docker](http://docker.com) >= 1.3
* [Fig](http://fig.sh) >= 1.0

## Installation

### 1. Deployment

Just clone the project and run ```fig up``` to build the containers and run the
application. Follow the steps on [https://github.com/svera/fig-silex](https://github.com/svera/fig-silex) if you need more information.

### 2. Register application

Lemur uses Github authentication for access control, so you need to register your recently deployed Lemur installation in Github. To do that,
go to your user settings and choose *Applications* > *Register new application*. Fill in the form, putting ```<your domain>/auth/github/callback```in the *Authorization callback URL* field.
You will get a client ID and a client secret key when done, put them into ```src/config/secrets.php``` (Use ```src/config/secrets.php.sample``` as an example). Remember, DO NOT UPLOAD THIS FILE TO ANY PUBLIC REPOSITORY!

### 3. Register webhooks

The last step is to configure your repositories to link some events so they can be registered by Lemur. To do that, we use the webhooks provided
by the supported platforms:

#### Github

Go to the repository settings and select *Webhooks & Services > Webhooks*. Once there, associate ```<your domain>/pull-request``` to the *Pull Request* and *Pull Request review comment* events.

#### Gitlab

### Additional considerations

For development, you also need a way for external services to reach
your environment. For that, you can use [ngrok](https://ngrok.com/).


## Useful commands

### Connect to the MongoDB instance client

Type ```fig run mongo mongo mongo/lemur-dev``` (development) or ```fig run mongo mongo mongo/lemur``` (production).

### Tests

Type ```fig run app phpunit src/tests``` to run all tests.

### PHP code sniffer

Type ```fig run app phpcs ./src --standard=psr2``` to check souce code for style errors.