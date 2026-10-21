# Releasing the plugin

The plugin ships the bundle built by the widget repository (`dappros/ethora-ai-chat-widget`). A plugin release is four steps. Steps 1 and 2 are automatic when the secrets below are set.

1. **Release the widget.** In the widget repository run the *Release* workflow (or `npm run release:ci`). It publishes to npm and creates a GitHub release with `ethora_assistant.js`, the pdf.js files (`pdf.min.mjs`, `pdf.worker.min.mjs`) and `SHA256SUMS` attached.
2. **Update the plugin.** The widget release starts the *Update the bundled widget* workflow here when the widget repository has the `WP_REPO_TOKEN` secret. To start it by hand:
   ```bash
   gh workflow run update-widget.yml -R dappros/ethora-wp-plugin -f widget_tag=v26.10.1
   ```
   It downloads the release files, checks them against `SHA256SUMS`, refuses a minified bundle, writes `assets/js/ethora_assistant.js` and `assets/js/pdfjs/`, sets the plugin version (header, `ETHORA_PLUGIN_VERSION`, `@version`, `Stable tag`) and adds a changelog line. It then runs the publish dry run with those files and opens a pull request. It never publishes.
3. **Merge the pull request** after a look at the diff.
4. **Publish.** Merging a version change into `main` starts *Publish to WordPress.org*, which waits for approval in the `wordpress-org` environment and then updates the WordPress.org SVN (trunk, `tags/<version>`, listing assets). See `.github/PUBLISHING.md`.

## Dry runs

- Update workflow: `gh workflow run update-widget.yml -R dappros/ethora-wp-plugin -f widget_tag=v26.10.1 -f dry_run=true` does everything except the push and the pull request, and prints the diff.
- Publish workflow: run it by hand with `publish=false` (the default). It validates the version and the bundle, reads the SVN repository and prints the plan. It uploads nothing.
- On a machine: `node scripts/update-widget.mjs --tag v26.10.1` (needs `gh` and `svn`) changes the files in your clone; `scripts/publish-wp.sh 1.8.1` prints the publish plan.

## Versions

The plugin version is SemVer `1.x.y`, separate from the widget version. The update workflow keeps the version in the plugin header if it is not published yet. If it is already published, it bumps the patch number of the newest published version. The version is changed in the plugin header, the version constant, the `@version` tag and `Stable tag`.

## The bundle must be readable

WordPress.org requires human-readable code. The update script refuses a minified bundle, and the publish script refuses one too.

## Setup, once

- Secrets in this repository: `WP_SVN_USERNAME` and `WP_SVN_PASSWORD` (the WordPress.org account that has commit access to the plugin; the SVN password is set in the account's Account & Security page and is not the login password).
- Environment `wordpress-org`, with required reviewers if you want an approval step.
- In the widget repository, the secret `WP_REPO_TOKEN`: a fine-grained token for this repository with the repository permission Actions: read and write.
- Repository setting "Allow GitHub Actions to create and approve pull requests", so the update workflow can open its pull request.
