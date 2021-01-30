# neoan3-cli
[![Build Status](https://travis-ci.com/neoan3/cli.svg?branch=master)](https://travis-ci.com/neoan3/cli)
[![Maintainability](https://api.codeclimate.com/v1/badges/29a1891ab06fe6be7b2e/maintainability)](https://codeclimate.com/github/neoan3/cli/maintainability)
[![neoan3](https://img.shields.io/badge/official%20tool-neoan3-blue.svg?style=flat&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAACVklEQVRYR+2VT0iTYRjAf990i7nNNs1viyiT/SkPEbKioMQygrIOYSpE1w51sA4RhAhegg5Rp87dghAWFURBVxUFbUZEpW1LTdwm+K/Npdv8OqzZt3dzrg56+X7H53lenh/P8/K+0qHmowrbiE4MbDWagCagCWgCJQnIrmUxVBCjNUn7o6803ZjE7o6L6YIUFZDdcVq6gjTfmhBTeeh3pDnfFaJq7wpTHyycvjlJS1dwU/lyMQBg98Txtocxy0n8PjvjfVaxJBedwtnbE9TU/eLT2yqCAzaCg1Y8jfOc6pwkFtUz3OsgOm4STyKpPyO7J463I0yFLYX/uUyg3wpIqvLCNF2f4uCZeaIBIy+7naylVIOVFJwnFvC2RonNlTPS6yAy9ldkfQIXugMYK9OM+GRCQzsppXEGBYtjhZWYjncPa3ObAygSgT4bgT4rdccWabw2TWKpjNf3nIB6BTpYA5R/+Jyr9iUwVScZeLIHc3WS2KxBLMlBUTI91DcvbwVHOsIYbSne+2SCAxuvwGBK0fZgDEtNitEXNQw93S2W/CGzgobWKIn5coaFFeQIZMneBfOuFH6fzHi/FdbUIgrn7n6n1vuTmS9GXvW4hDygU3CfXKDhcoTYrD5v91kKCmSR3ZmJVDpWedZZvx4/fCnC8asREks6fHc8xOfyR3/l8WcWZwwb3v4sRQWyyK5lot8qAHDUx7nYE0CS4M39/fwYrRSqM6jPFKMkATVlhjSOA8sYTGlCg5u8DyVQ8CEqRnq1jOmPFjH83xR9ircCTUAT0AQ0gW0X+A3UX84kMp7NmAAAAABJRU5ErkJggg==&colorA=2d4235)](https://neoan3.rocks)


## Automate your [neoan3](https://neoan3.rocks) needs


1. [Requirements](#Requires)
2. [Installation](#installation)
3. Creating new ...
    1. [app](#starting-a-new-neoan3-application)
    2. [component](#new-component)
    3. [frame](#new-frame)
    4. [model](#new-model)
4. [Installing (third party) scripts](#install)
5. [Reusing components/frames/models](#add)
6. [Handling credentials](#credentials)
7. [Database migration](#migrate)
8. [Publishing components/frames/models to be reused](#publish)
9. [File creation templating](#templates)
10. [Development server](#development-server-shortcut)
11. [Reporting issues](#reporting-issues)
12. [Collaboration](#collaborators-wanted)
    

### Requires
- PHP
- composer
- GIT

### Installation

```  
composer global require neoan3/neoan3
```

## Starting a new neoan3 application

1. create a new folder and navigate into it

    e.g. `mkdir myApp`

    `cd myApp`

2. run neoan3 new app command

    e.g. `neoan3 new app myApp`
3. run in your local webserver

_Note:_ The last parameter (e.g. myApp) should be the web-folder the app is run under. If you are running the instance on the root,
please omit the last parameter. **Deployment**: It is likely that you will have to change the .htaccess (when running Apache) for your deployment target.
In most cases, changing the RewriteBase should be enough.

### new component
`neoan3 new component [component-name]`

This command will guide you through the creation of a new component, prefilling the controller according to your choices.
- api (generates get & post functions using a particular frame)
- route (generates init-functions resulting in the component acting as a valid route)
- custom element (currently empty class)


### new frame
`neoan3 new frame [frame-name]`

This command generates a new frame.
### new model
`neoan3 new model [model-name]`

This command creates a new model.


## install (not yet implemented)

`neoan3 install [url]`

This command executes external installation scripts in cases where composer cannot.
In most cases you want to use "add" instead of "install" to ensure proper collaboration.
This command is normally used to simplify installation of 3rd party applications.

## add (not yet implemented)
`neoan3 add [destination] [package] ([repository-endpoint])`

Adding components makes the following assumptions:
-  the component is registered as a composer-package
-  it is either targeted at being a frame, a model or a component

for all other packages, please use the respective package manager (e.g. composer or npm) or version control system (e.g. GIT).
>neoan3 apps are "regular" composer packages. Please include them using `composer require`

_example_

`neoan3 add frame neoan3-frame/kit`

You can also add the repository-endpoint parameter if you have private repositories or want to work without publishing to packagist.
Please include the full url in these cases.
>_works with bitbucket & github_

_example_

`neoan3 add model custom-model/products https://github.com/yourName/yourPackage.git`

Please note that the name (here: custom-model/products) must be the name of specified in your composer.json of the neoan3-entity.
See [publish](#publish).

## credentials

It is recommended to store static credentials (like smtp, API-tokens etc) outside the web root. 
This command attempts to mange such credentials in a folder "credentials" and a file credentials.json

`neoan3 credentials` 

In its most simplistic form, a neoan3 implementation could look like this (in a frame):
```PHP
$credentials = getCredentials();
// e.g. sendgrid
$sendgridCredentials = $credentials['sendgrid'];
```

_NOTE_ 

By default the cli tries to work with a folder "credentials" in the root of your current drive.
You can influence this behavior by setting it to another location e.g.

`neoan3 set credential-path /home/myUser`

Remember to pass in the same path within your neoan3 project when calling the global function _getCredentials($path)_.

## migrate

Currently supports SQL only. 

`neoan3 migrate models [direction]` 

### migrate down

`neoan3 migrate models down` 

This generates migrate.jsons from the connected database structure. The following assumptions are made:

Tables starting with a particular model-name are associated with that model. Example: If a model "user" exists tables starting with "user" are considered.
This would include table-names like "user", "user_password" or "userEmail". 
The recommended way for neoan3 is to follow a snake_case naming for tables and columns.

### migrate up

`neoan3 migrate models up` 

This creates or alters tables in your connected database based on structural declarations present in your migrate.json files in the folder of models.
It is important to know that removing a column in your declaration will NOT remove the column from the database, while adding a column will generate the column in your database.

## publish (not yet implemented)

`publish [entity-type] [entity-name]`

_example_

`publish model user`

The publish-command transforms a local neoan3 entity into a composer package. Dependencies are taken care of automatically.
The command will also ask you whether you directly want to publish on github. To do so, please ensure:

- you have git installed
- you have registered you identity (config)
- you have a valid token for the intended username
- created a remote repository (e.g. via github.com)

Please refer to Git documentation in order to achieve the above.

Since you have a valid composer.json in your repository now, you may publish on packagist as well.

## Templates
You can influence the generated output with templates. To do so, place a folder _template in your project.
Basic variable support works with double curly braces without spaces ( {{name}} )
The following files are respected:

| template file | entity | variable(s) | Note |
| --- | --- | --- | --- |
| frame.php | frame | name  ||
| model.php | model | name  ||
| api.php | component (API) | name , frame ||
| route.php | component (Route) | name , frame ||
| view.html | component (Route) | name  ||
| ce.php | Custom element | name  | *PHP files for custom element is only generated if template is present |
| ce.js | Custom element | name | |
| ce.html | Custom element | name | |
| *.json | credentials | | uses own name to hook into boilerplating credential creation |

**_Casing:_** By default variables are translated to PascalCase. 
Depending on your needs, you have the following possibilities for your casing:

| Modifier | Example | Transformation |
| --- | --- | --- |
| .lower | {{name.lower}} | Converts "name" to lower case |
| .camel | {{name.camel}} | Converts "name" to camelCase |
| .pascal | {{name.pascal}} | Forces PascalCase (for edge cases) |

# Development server shortcut

Running `neoan3 develop` will start the built-in PHP server using the provided router-script.

# Reporting issues

The GitHub repo [neoan3-cli](https://github.com/neoan3/cli) is actively maintained. Please report issues there.

# Collaborators wanted
Over 10.000 neoan3 applications are waiting for documentation. I'd love to have some people to help me:

- document neoan3 (framework)
- document neoan3-cli
- document various neoan3 composer apps
- create tutorials

If you are interested, please fel free to get in touch @ https://github.com/sroehrl

## Contributors
[sroehrl](https://github.com/sroehrl)

