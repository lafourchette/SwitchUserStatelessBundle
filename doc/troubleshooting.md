# Troubleshooting

This is a list of common pitfalls on using SwitchUserStatelessBundle, and how to avoid them.

## Use with NelmioCorsBundle

In the configuration [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle), we must declare `X-Switch-user` in array `allow_headers`

```yml
# app/config/config.yml

nelmio_cors:
    # ...
    paths:
        "^/api":
            # ...
            allow_headers: ['X-Switch-User']
```
