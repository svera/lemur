# Lemur

If you have to deal whith multiple repositories along several platforms such
as [Github](https://github.com) or [Gitlab](https://about.gitlab.com), and do a code review every time a new pull/merge request.
is created in any of those repositories, you already know what a pain is to track
have you already reviewed, which ones are waiting to be merged for too long, etc.

Lemur is a simple app which provides an unified view of all pull/merge requests
waiting to be reviewed or merged. To do that, it just needs to be connected to the
source repositories using [webhooks](https://developer.github.com/webhooks/).

## Features

* Show pull/merge requests from multiple repositories and services.
* Show +1/-1 comments in each pull request (Github only).
* Show pull requests' waiting time.

## Installation

### Requirements

* [Docker](http://docker.com) >= 1.3
* [Fig](http://fig.sh) >= 1.0

For development testing, you also need a way for external services to reach
your environment. For that, you can use [ngrok](https://ngrok.com/).

## Use

Right now, Lemur supports Github and Gitlab 