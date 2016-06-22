SwitchUserStatelessBundle
-------------------------

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lafourchette/SwitchUserStatelessBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lafourchette/SwitchUserStatelessBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lafourchette/SwitchUserStatelessBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lafourchette/SwitchUserStatelessBundle/?branch=master)
[![Build Status](https://travis-ci.org/lafourchette/SwitchUserStatelessBundle.svg?branch=master)](https://travis-ci.org/lafourchette/SwitchUserStatelessBundle)
[![Dependency Status](https://www.versioneye.com/user/projects/5710a925fcd19a0039f17030/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5710a925fcd19a0039f17030)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fb95e39f-09a5-4c3e-a004-c7b93a8bd725/mini.png)](https://insight.sensiolabs.com/projects/fb95e39f-09a5-4c3e-a004-c7b93a8bd725)

This bundle provides impersonating feature (switch user) for API use.

## Install

Install this bundle through [Composer](https://getcomposer.org/):

```
composer require lafourchette/switch-user-stateless-bundle
```

Then, update your application kernel:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new LaFourchette\SwitchUserStatelessBundle\SwitchUserStatelessBundle(),
        ];

        // ...
    }
}
```

Finally, update your firewalls as following:

```yml
# app/config/security.yml

security:
    firewalls:
        main:
            # ...
            stateless: true
            switch_user_stateless: true
```

## Configuration

You can configure the parameter used in HTTP request and role of user who switch in your config.yml. The examples below are the default values.

```yml
# app/config/config.yml

switch_user_stateless:
    parameter: 'X-Switch-User'
    role: 'ROLE_ALLOWED_TO_SWITCH'
```

## Usage

To use this feature, you need to add a `X-Switch-User` header to issued HTTP request containing the username of the
user you want to switch:

```
X-Switch-User: johndoe
```

For security reasons, this feature is only accessible for users with `ROLE_ALLOWED_TO_SWITCH` permission. Admin users
have this permission by default.

## Troubleshooting

[Solving problems here](https://github.com/lafourchette/SwitchUserStatelessBundle/tree/master/doc/troubleshooting.md)
