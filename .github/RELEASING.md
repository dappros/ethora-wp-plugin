# Releasing the plugin

The plugin ships the bundle built by the widget repository (`dappros/ethora-ai-chat-widget`). A plugin release is four steps.

1. **Release the widget.** In the widget repository run `npm run release:ci`. It publishes to npm and attaches `ethora_assistant.js` to the GitHub release.
2. **Update the plugin.** The widget release starts the *Update the bundled widget* workflow here when the widget repository has the `WP_REPO_TOKEN` secret. To start it by hand:
   ```bash
   gh workflow run update-widget.yml -R dappros/ethora-wp-plugin -f widget_tag=v26.10.1
   ```
   It opens a pull request with the new bundle, the plugin version and a changelog line. Before opening it, it runs the publish dry run with the updated files. It never publishes.
3. **Merge the pull request** after a look at the diff.
4. **Publish** with the *Publish to WordPress.org* workflow. Run it as a dry run first, then with `publish=true`. The publish job needs approval in the `wordpress-org` environment.
   ```bash
   gh workflow run publish-wp.yml -R dappros/ethora-wp-plugin -f version=1.8.0 -f publish=false
   gh workflow run publish-wp.yml -R dappros/ethora-wp-plugin -f version=1.8.0 -f publish=true
   ```

## Versions

The plugin version is SemVer `1.x.y`, separate from the widget version. The update workflow keeps the version in the plugin header if it is not published yet. If it is already published, it bumps the patch number of the newest published version. The version is changed in the plugin header, the version constant and `Stable tag`.

## The bundle must be readable

WordPress.org requires human-readable code. The update script refuses a minified bundle, and the publish script refuses one too.

## Setup, once

- Secrets in this repository: `WP_SVN_USERNAME` and `WP_SVN_PASSWORD`.
- Environment `wordpress-org`, with required reviewers if you want an approval step.
- In the widget repository, the secret `WP_REPO_TOKEN`: a token that can run workflows in this repository.
- Repository setting "Allow GitHub Actions to create and approve pull requests", so the update workflow can open its pull request.
