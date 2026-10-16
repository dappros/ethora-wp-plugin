#!/usr/bin/env bash
#
# Publish the Ethora Chat Assistant plugin to the WordPress.org SVN repository.
#
# Usage:
#   scripts/publish-wp.sh <version>          dry run: validate, read SVN, print the plan
#   scripts/publish-wp.sh <version> --yes    commit trunk and tags/<version> to SVN
#
# <version> must be SemVer 1.x.y and equal both the "Version:" header of
# ethora-assistant-plugin.php and the "Stable tag:" line of readme.txt.
#
# The dry run only reads from SVN (checkout and ls). Nothing is written to the
# remote repository unless --yes is given. With --yes, WP_SVN_USERNAME and
# WP_SVN_PASSWORD must be set. The password is never printed.
#
# What gets published: everything in the repo root except the excludes below,
# copied to trunk/, then trunk/ is copied to tags/<version>/. The plugin's own
# assets/ folder (runtime CSS, JS and images) goes to trunk/assets/. The SVN
# repository root assets/ folder (wordpress.org listing banners, icons and
# screenshots) is synced from wp-assets/ in this repository, so the listing
# images are versioned with the code.
#
# WP_SVN_URL overrides the SVN URL. It exists only so the commit path can be
# tested against a local repository. Leave it unset for real releases.

set -euo pipefail

SVN_URL="${WP_SVN_URL:-https://plugins.svn.wordpress.org/ethora-chat-assistant}"
MAIN_FILE="ethora-assistant-plugin.php"
README_FILE="readme.txt"
BUNDLE_FILE="assets/js/ethora_assistant.js"
BUNDLE_MIN_LINES=500
BUNDLE_MAX_AVG_LINE=300

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

die() {
  echo "ERROR: $*" >&2
  exit 1
}

warn() {
  echo "WARNING: $*" >&2
}

usage() {
  echo "Usage: $0 <version> [--yes]" >&2
  echo "  <version>  SemVer 1.x.y, for example 1.7.3" >&2
  echo "  --yes      commit to the WordPress.org SVN repository (default is a dry run)" >&2
  exit 2
}

# --- Arguments ---------------------------------------------------------------

[ "$#" -ge 1 ] || usage
VERSION="$1"
shift

YES=0
for arg in "$@"; do
  case "$arg" in
    --yes) YES=1 ;;
    *) usage ;;
  esac
done

VERSION_RE='^1\.[0-9]+\.[0-9]+$'
if ! [[ "$VERSION" =~ $VERSION_RE ]]; then
  die "version '$VERSION' is not valid. Expected SemVer 1.x.y, for example 1.7.3."
fi

# --- Local validation (no network) -------------------------------------------

[ -f "$ROOT/$MAIN_FILE" ] || die "$MAIN_FILE not found in $ROOT"
[ -f "$ROOT/$README_FILE" ] || die "$README_FILE not found in $ROOT"

HEADER_VERSION="$(awk '/^[[:space:]]*\*[[:space:]]*Version:/ { sub(/^[^:]*:[[:space:]]*/, ""); sub(/[[:space:]]+$/, ""); print; exit }' "$ROOT/$MAIN_FILE")"
STABLE_TAG="$(awk '/^Stable tag:/ { sub(/^[^:]*:[[:space:]]*/, ""); sub(/[[:space:]]+$/, ""); print; exit }' "$ROOT/$README_FILE")"

[ -n "$HEADER_VERSION" ] || die "no 'Version:' header found in $MAIN_FILE"
[ -n "$STABLE_TAG" ] || die "no 'Stable tag:' line found in $README_FILE"
[ "$HEADER_VERSION" = "$VERSION" ] || die "$MAIN_FILE has 'Version: $HEADER_VERSION' but you asked to publish '$VERSION'. Bump the header first."
[ "$STABLE_TAG" = "$VERSION" ] || die "$README_FILE has 'Stable tag: $STABLE_TAG' but you asked to publish '$VERSION'. Bump it first."
echo "OK: version $VERSION matches the Version header and the Stable tag"

if command -v php >/dev/null 2>&1; then
  php -l "$ROOT/$MAIN_FILE" || die "php -l reports a syntax error in $MAIN_FILE"
  echo "OK: php -l $MAIN_FILE"
else
  warn "php is not installed, so $MAIN_FILE was not syntax-checked"
fi

BUNDLE="$ROOT/$BUNDLE_FILE"
[ -f "$BUNDLE" ] || die "$BUNDLE_FILE is missing"
BUNDLE_STATS="$(awk '{ n++; total += length($0) } END { print n + 0, total + 0 }' "$BUNDLE")"
BUNDLE_LINES="${BUNDLE_STATS% *}"
BUNDLE_CHARS="${BUNDLE_STATS#* }"
if [ "$BUNDLE_LINES" -lt "$BUNDLE_MIN_LINES" ]; then
  die "$BUNDLE_FILE has $BUNDLE_LINES lines (minimum $BUNDLE_MIN_LINES). It looks minified, and WordPress.org requires readable source."
fi
if [ "$BUNDLE_CHARS" -gt $((BUNDLE_LINES * BUNDLE_MAX_AVG_LINE)) ]; then
  die "$BUNDLE_FILE has an average line length over $BUNDLE_MAX_AVG_LINE characters. It looks minified, and WordPress.org requires readable source."
fi
echo "OK: $BUNDLE_FILE is readable ($BUNDLE_LINES lines)"

command -v svn >/dev/null 2>&1 || die "svn is not installed (macOS: brew install svn, Debian/Ubuntu: apt-get install subversion)"
command -v rsync >/dev/null 2>&1 || die "rsync is not installed"

if [ "$YES" -eq 1 ]; then
  [ -n "${WP_SVN_USERNAME:-}" ] || die "WP_SVN_USERNAME is not set (required with --yes)"
  [ -n "${WP_SVN_PASSWORD:-}" ] || die "WP_SVN_PASSWORD is not set (required with --yes)"
fi

# --- Remote read: make sure the tag is new ------------------------------------

TAGS_LIST="$(svn ls --non-interactive "$SVN_URL/tags")"
if grep -Fqx "$VERSION/" <<< "$TAGS_LIST"; then
  die "tags/$VERSION already exists in the SVN repository. Choose a new version."
fi
echo "OK: tags/$VERSION does not exist yet"

# --- Checkout ----------------------------------------------------------------

WORKDIR="$(mktemp -d)"
trap 'rm -rf "$WORKDIR"' EXIT
SVN_DIR="$WORKDIR/svn"
TRUNK="$SVN_DIR/trunk"
TAG_DIR="$SVN_DIR/tags/$VERSION"

echo "==> Checking out $SVN_URL (trunk in full, tags listed only)"
svn checkout --quiet --non-interactive --depth immediates "$SVN_URL" "$SVN_DIR"
svn update --quiet --non-interactive --set-depth infinity "$TRUNK"
svn update --quiet --non-interactive --set-depth empty "$SVN_DIR/tags"

# --- Sync plugin files into trunk ---------------------------------------------

EXCLUDES=(
  --exclude=/.git
  --exclude=/.github
  --exclude=/scripts
  --exclude=/build
  --exclude=/wp-assets
  --exclude=/node_modules
  --exclude=/.gitignore
  --exclude=/.DS_Store
)

echo "==> Syncing plugin files into trunk"
# --delete-excluded also removes excluded paths that an earlier release left in
# trunk. .svn is protected, otherwise the working copy metadata would be deleted.
rsync -a --delete --delete-excluded --filter='P .svn' "${EXCLUDES[@]}" "$ROOT/" "$TRUNK/"

# Schedule new files for addition. Files and folders removed from the source are
# missing from disk, so svn status reports them with "!". Scheduling each one for
# deletion is safe even when its parent folder was already scheduled.
svn add --quiet --force "$TRUNK"
MISSING_LIST="$(svn status "$TRUNK" | sed -n 's/^![[:space:]]*//p')"
while IFS= read -r missing_path; do
  [ -n "$missing_path" ] || continue
  svn rm --quiet --force "$missing_path"
done <<< "$MISSING_LIST"

echo "==> Changes to trunk:"
CHANGES="$(cd "$TRUNK" && svn status)"
if [ -z "$CHANGES" ]; then
  echo "    (none)"
else
  printf '%s\n' "$CHANGES" | sed 's/^/    /'
fi

# --- Sync the listing assets (banners, icons, screenshots) to assets/ ---------
# They live in the repository root assets/ folder of SVN, not in trunk. The
# source is wp-assets/ in this repository. --delete also removes listing images
# that are no longer in wp-assets/, so the listing never shows a stale screenshot.
# .svn is protected, as above.

ASSETS_DIR="$SVN_DIR/assets"
if [ -d "$ROOT/wp-assets" ]; then
  echo "==> Syncing listing assets from wp-assets/"
  svn update --quiet --non-interactive --set-depth infinity "$ASSETS_DIR"
  rsync -a --delete --delete-excluded --filter='P .svn' "$ROOT/wp-assets/" "$ASSETS_DIR/"
  svn add --quiet --force "$ASSETS_DIR"
  MISSING_ASSETS="$(svn status "$ASSETS_DIR" | sed -n 's/^![[:space:]]*//p')"
  while IFS= read -r missing_asset; do
    [ -n "$missing_asset" ] || continue
    svn rm --quiet --force "$missing_asset"
  done <<< "$MISSING_ASSETS"
  echo "==> Changes to assets/:"
  ASSET_CHANGES="$(cd "$ASSETS_DIR" && svn status)"
  if [ -z "$ASSET_CHANGES" ]; then
    echo "    (none)"
  else
    printf '%s\n' "$ASSET_CHANGES" | sed 's/^/    /'
  fi
else
  echo "==> No wp-assets/ folder, listing assets are not touched"
fi

# --- Copy trunk to tags/<version> ---------------------------------------------

mkdir "$TAG_DIR"
rsync -a --exclude=.svn "$TRUNK/" "$TAG_DIR/"
svn add --quiet --force "$TAG_DIR"
TAG_FILES="$(find "$TAG_DIR" -type f | wc -l | tr -d '[:space:]')"
echo "==> tags/$VERSION will contain $TAG_FILES files"

# --- Dry run stops here -------------------------------------------------------

if [ "$YES" -ne 1 ]; then
  echo ""
  echo "DRY RUN: nothing was committed. Re-run with --yes to publish $VERSION."
  exit 0
fi

# --- Commit -------------------------------------------------------------------

echo "==> Committing trunk and tags/$VERSION to $SVN_URL"
svn commit --non-interactive --no-auth-cache \
  -m "Release $VERSION" \
  --username "$WP_SVN_USERNAME" \
  --password "$WP_SVN_PASSWORD" \
  "$TRUNK" "$TAG_DIR"

echo "==> Published $VERSION"
