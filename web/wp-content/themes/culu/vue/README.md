# Bundled Vue apps

The CULU theme includes bundled Vue apps that are registered and invoked using shortcodes.

| App                   | Shortcode          | Dev path    |
| --------------------- | ------------------ | ----------- |
| Software availability | `[software_avail]` | `/software` |


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

## Build for production

When run in Wordpress instances hosted on Pantheon, the CULU theme requires compiled and minified assets to utilize the Vue apps.

Be sure to build the apps and commit the bundled assets prior to submitting a PR for review.

```
yarn build
```

> Bundled assets are output to `vue/dist`
