# Lemur

If you have to deal whith multiple repositories along several platforms such
as [Github](https://github.com) or [Gitlab](https://about.gitlab.com), 
and do a code review every time a new pull/merge request
is created in any of those repositories, you already know what a pain is to track
have you already reviewed, which ones are waiting to be merged for too long, etc.

Lemur is a simple app which provides an unified view of all pull/merge requests
waiting to be reviewed or merged. To do that, it just needs to be connected to the
source repositories using [webhooks](https://developer.github.com/webhooks/).

## Features

* Show pull/merge requests from multiple repositories and services.
* Show +1/-1 comments in each pull request (Github only).
* Show pull requests' waiting time.

## Requirements

* [Docker](http://docker.com) >= 1.3
* [Fig](http://fig.sh) >= 1.0

## Installation

Just clone the project and run ```fig up``` to build the containers and run the
application. Follow the steps on [https://github.com/svera/fig-silex](https://github.com/svera/fig-silex) if you need more information.

For development testing, you also need a way for external services to reach
your environment. For that, you can use [ngrok](https://ngrok.com/).

## Use

Right now, Lemur supports Github and Gitlab. You need to configure some webhooks
on these platforms in order to receive the needed information from them.

### Github