# Environment Settings

Some of the settings in `behat.yml` are environment specific. For example, the `base_url` may be `http://test.example.dev` on your local development environment, while on a test server it might be `http://test.example.com`.

If you intend to run your tests on different environments, these sorts of settings should not be added to your `behat.yml`. Instead, they should be exported in an environment variable.

Before running tests, Behat will check the `BEHAT_PARAMS` environment variable and add these settings to the ones that are present in `behat.yml` (settings from this file takes precedence). This variable should contain a JSON object with your settings.

Example JSON object:

```JavaScript
{
  "extensions": {
    "Behat\\MinkExtension": {
      "base_url": "http://development.dev"
    }
  }
}
```

To export this into the ``BEHAT_PARAMS`` environment variable, squash the JSON object into a single line and surround with single quotes:

```Shell
export BEHAT_PARAMS='{"extensions":{"Behat\\MinkExtension":{"base_url":"http://development.dev"}}}'
```
