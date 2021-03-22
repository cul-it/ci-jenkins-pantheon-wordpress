# Bundled Vue apps

- [Dev Quickstart](#dev-quickstart)
  - [Lint and auto format files](#lint-and-auto-format-files)
- [Environment variables & sensitive data](#environment-variables--sensitive-data)
  - [Local Development](#local-development)
  - [Pantheon](#pantheon)
- [Build for production](#build-for-production)

The CULU theme includes bundled Vue apps that are registered and invoked using shortcodes.

> Refer to the wiki for [detailed shortcodes documentation](https://github.com/cul-it/wp-cul-theme-culu/wiki/Shortcodes)

| App                   | Shortcode          | Attributes | Dev path    |
| --------------------- | ------------------ | ---------- | ----------- |
| Software availability | `[software_avail]` |            |`/software` |
| Staff profiles        | `[staff]`          | `dept`     | `/staff`    |


> Dev path identifies absolute path from `http://localhost:8080` when running vue-cli development server

## Dev Quickstart

1. Clone this repo into the themes directory of a local Wordpress instance
   ```
   cd wp-content/themes
   clone git@github.com:cul-it/wp-cul-theme-culu.git culu-dev
   ```

1. Install the dependencies
   ```
   cd culu-dev/vue
   yarn install
   ```

1. Serve the Vue apps for development (with hot-reloads)
   ```
   yarn serve
   ```

   > The vue-cli development server will be available at http://localhost:8080. See the [table above](#bundled-vue-apps) for a list of apps and their respective paths.

1. Update script registrations in `inc/template-functions.php#vue_bundled_assets()` to point to the respective URLs for the vue-cli development server (see inline comments)
   > **IMPORTANT!** Do not commit this temporary change

1. Activate the theme via the Wordpress admin and use shortcodes as needed

### Lint and auto format files
```
yarn lint
```

## Environment variables & sensitive data

### Local Development

Regardless of local development via [Lando](https://lando.dev/) or [Pantheon Localdev](https://pantheon.io/localdev), management of secrets remains the same.

1. Copy `secrets.json.example` to `secrets.json`
   ```
   cp secrets.json.example secrets.json
   ```

1. Edit `secrets.json` and replace all instances of `CHANGEME` to appropriate values

### Pantheon

Each secret must be manually set in every environment (`dev`, `test`, `live`) for each Pantheon instance via the [terminus secrets plugin](https://github.com/pantheon-systems/terminus-secrets-plugin).

Example commands:

* List all secrets in `dev` environment of olinuris Pantheon instance:
  ```
  terminus secrets:list uls-olinuris-library-cornell-edu.dev
  ```

* Add a secret in `dev` environment of olinuris Pantheon instance:
  ```
  terminus secrets:set uls-olinuris-library-cornell-edu.dev SASSAFRAS_USER CHANGEME

> See [pantheon-systems/terminus-secrets-plugin README](https://github.com/pantheon-systems/terminus-secrets-plugin#usage) for full usage details.

## Build for production

When run in Wordpress instances hosted on Pantheon, the CULU theme requires compiled and minified assets to utilize the Vue apps.

Be sure to build the apps and commit the bundled assets prior to submitting a PR for review.

```
yarn build
```

> Bundled assets are output to `vue/dist`
