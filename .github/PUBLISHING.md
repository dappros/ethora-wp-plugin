# Publishing to WordPress.org

`.github/workflows/publish-wp.yml` updates the WordPress.org SVN repository of the plugin (`https://plugins.svn.wordpress.org/ethora-chat-assistant`): trunk, `tags/<version>` and, when the repository has a `wp-assets/` folder, the listing images in the SVN `assets/` folder. The work is done by `scripts/publish-wp.sh`, which can also be run on a machine.

WordPress.org serves the version named by `Stable tag` in `readme.txt`. The script refuses to publish unless the version argument equals both the `Version:` header of `ethora-assistant-plugin.php` and the `Stable tag`, and it refuses a version whose `tags/<version>` already exists.

## When it runs

| Trigger | What happens |
|---|---|
| Push to `main` where the `Version:` header differs from the previous commit | Dry run, then publish after approval |
| Push of a tag `vX.Y.Z` (X.Y.Z must equal the header) | Dry run, then publish after approval |
| Run by hand, `publish` off (default) | Dry run only |
| Run by hand, `publish` on | Dry run, then publish after approval |
| Any other push to `main` | Nothing (the detect job says so in the run summary) |

If `tags/<version>` already exists on WordPress.org, the run stops after the detect job. That is how a tag push after a merge, or a repeated run, does no harm.

The usual path is automatic: the widget release starts *Update the bundled widget*, which opens a pull request with the new bundle and version (see `RELEASING.md`). Merging that pull request is the push to `main` with a changed version.

## The dry run

The dry-run job validates the version, checks that the bundle `assets/js/ethora_assistant.js` is readable (not minified), reads the SVN repository (read only), syncs the files into a scratch checkout and prints the list of changes and the file count of the new tag. It uploads nothing and needs no secrets.

By hand:

```bash
gh workflow run publish-wp.yml -R dappros/ethora-wp-plugin -f version=1.8.1
```

Add `-f widget_tag=v26.10.21` to take the bundle and the pdf.js files from a widget release instead of the files committed here. Add `-f publish=true` to publish.

On a machine (needs `svn` and `rsync`):

```bash
scripts/publish-wp.sh 1.8.1          # dry run
WP_SVN_USERNAME=... WP_SVN_PASSWORD=... scripts/publish-wp.sh 1.8.1 --yes
```

## Setup, once

- Secrets `WP_SVN_USERNAME` and `WP_SVN_PASSWORD` in the repository or in the `wordpress-org` environment. The user needs commit access to the plugin (the Committers list on the plugin's Advanced page). The SVN password is the one set in the account's Account & Security page, not the login password.
- Environment `wordpress-org`. Add required reviewers so that every publish waits for an approval. Without reviewers the publish job starts as soon as the dry run passes.
- Optional repository variable `WP_AUTO_PUBLISH`. Set it to `false` to stop the push and tag triggers after the dry run (manual runs with `publish` on still publish).

## Notes

- The bundle committed on `main` must be the readable build. The publish script stops on a minified one. The update workflow replaces it with the readable bundle of a widget release.
- Listing images (banners, icons, screenshots) are synced from `wp-assets/` by the version of `scripts/publish-wp.sh` that has that step. Without a `wp-assets/` folder the SVN `assets/` folder is not touched.
- A pull request opened by the update workflow uses the built-in token, so GitHub does not start other workflows for it. The publish workflow runs on the merge, which a person does.
