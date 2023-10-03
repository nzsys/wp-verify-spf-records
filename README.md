# WordPress Verify SPF records

This is a plugin for validating SPF records in WordPress. Verify SPF records from FQDN or email address. joke.
なんの意味も感じない無駄コードを産み出しことをここに記す。

## Requirements

- PHP: `^8.1 | ^8.2`

## Installing
When using docker.
```bash
docker-compose up -d
```
```bash
mv ./wp-verify-spf-records ./wordpress/wp-content/plugins/
```

## Usage
After moving to the plugin directory, log in to the admin screen, enable the plugin, and click SPF record verification in the settings to use it. It is a good idea to add processing to the part of the code that is used as envelope from after successful verification according to your own environment.
