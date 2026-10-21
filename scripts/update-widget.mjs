#!/usr/bin/env node
// Updates the plugin from a widget release.
//
//   node scripts/update-widget.mjs --tag v26.10.1
//
// What it does:
//   1. Downloads ethora_assistant.js, the pdf.js files and SHA256SUMS from the widget
//      release (gh release download). The pdf.js files and the checksums are optional
//      for releases made before the widget attached them.
//   2. Checks the checksums when the release has them, and refuses a bundle that is
//      not human-readable (a WordPress.org requirement).
//   3. Writes the bundle to assets/js/ethora_assistant.js and the pdf.js files to
//      assets/js/pdfjs/.
//   4. Keeps the plugin version if it is not published yet. If it is already
//      published, bumps the patch number of the newest published version. The
//      version is updated in the plugin header, the version constant and Stable tag.
//   5. Adds a changelog line under that version.
//
// It changes files only. Committing and publishing are separate steps.
//
// Test options:
//   --assets-dir <dir>      use the files in this folder instead of downloading the release
//                           (ethora_assistant.js, optionally pdf.min.mjs, pdf.worker.min.mjs, SHA256SUMS)
//   --bundle <file>         use a local bundle file instead of the release
//   --published 1.7.2,1.7.1 use this list instead of reading the SVN tags
import { createHash } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { appendFileSync, existsSync, mkdirSync, mkdtempSync, readFileSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join } from 'node:path';

const WIDGET_REPO = 'dappros/ethora-ai-chat-widget';
const SVN_TAGS = 'https://plugins.svn.wordpress.org/ethora-chat-assistant/tags';
const BUNDLE = 'assets/js/ethora_assistant.js';
// Release asset name -> path in the plugin.
const PDFJS = {
  'pdf.min.mjs': 'assets/js/pdfjs/pdf.min.mjs',
  'pdf.worker.min.mjs': 'assets/js/pdfjs/pdf.worker.min.mjs',
};
const PLUGIN_FILE = 'ethora-assistant-plugin.php';
const README = 'readme.txt';

const args = process.argv.slice(2);
const option = (name) => {
  const i = args.indexOf(name);
  return i >= 0 ? args[i + 1] : undefined;
};

function fail(message) {
  console.error(`update-widget: ${message}`);
  process.exit(1);
}

function output(name, value) {
  if (process.env.GITHUB_OUTPUT) appendFileSync(process.env.GITHUB_OUTPUT, `${name}=${value}\n`);
}

const tag = option('--tag');
if (!tag || !/^v\d{2}\.(?:[1-9]|1[0-2])\.(?:[1-9]|[12]\d|3[01])$/.test(tag)) {
  fail('--tag must look like v26.10.1, with no leading zeros');
}

// --- 1. the release files -----------------------------------------------------

let dir = option('--assets-dir');
let bundlePath = option('--bundle');
if (!dir && !bundlePath) {
  dir = mkdtempSync(join(tmpdir(), 'widget-'));
  try {
    execFileSync(
      'gh',
      [
        'release', 'download', tag, '--repo', WIDGET_REPO, '--dir', dir, '--clobber',
        '--pattern', 'ethora_assistant.js',
        '--pattern', 'pdf.min.mjs',
        '--pattern', 'pdf.worker.min.mjs',
        '--pattern', 'SHA256SUMS',
      ],
      { stdio: ['ignore', 'pipe', 'pipe'] }
    );
  } catch (err) {
    fail(`cannot download the files of ${WIDGET_REPO} ${tag}: ${String(err.stderr || err.message).trim()}`);
  }
}
if (!bundlePath) bundlePath = join(dir, 'ethora_assistant.js');
if (!existsSync(bundlePath)) fail(`${tag} has no ethora_assistant.js`);
const bundleBytes = readFileSync(bundlePath);
const bundle = bundleBytes.toString('utf8');

// pdf.js files the release carries (the widget loads them from pdfjs/ next to the bundle).
const pdfFiles = {};
if (dir) {
  for (const [asset, target] of Object.entries(PDFJS)) {
    if (existsSync(join(dir, asset))) pdfFiles[target] = readFileSync(join(dir, asset));
  }
}
const pdfCount = Object.keys(pdfFiles).length;
if (pdfCount !== 0 && pdfCount !== Object.keys(PDFJS).length) {
  fail(`${tag} carries only some of the pdf.js files (${Object.keys(PDFJS).join(', ')})`);
}

// Checksums, when the release has them.
const sums = dir && existsSync(join(dir, 'SHA256SUMS')) ? readFileSync(join(dir, 'SHA256SUMS'), 'utf8') : null;
if (sums) {
  const expected = Object.fromEntries(
    sums.split('\n').filter(Boolean).map((line) => {
      const [hash, name] = line.trim().split(/\s+/);
      return [name, hash];
    })
  );
  const sha256 = (data) => createHash('sha256').update(data).digest('hex');
  const check = (name, data) => {
    if (!expected[name]) fail(`SHA256SUMS of ${tag} has no entry for ${name}`);
    if (expected[name] !== sha256(data)) fail(`${name} does not match SHA256SUMS of ${tag}`);
  };
  check('ethora_assistant.js', bundleBytes);
  for (const [asset, target] of Object.entries(PDFJS)) {
    if (pdfFiles[target]) check(asset, pdfFiles[target]);
  }
  console.log(`Checksums of ${tag} match.`);
} else {
  console.log(`Warning: ${tag} has no SHA256SUMS, the files were not checksum-verified.`);
}

// --- 2. readable, not minified ------------------------------------------------

const lines = bundle.split('\n');
const average = bundle.length / lines.length;
const problems = [];
if (lines.length < 5000) problems.push(`only ${lines.length} lines`);
if (average > 200) problems.push(`average line length is ${Math.round(average)}`);
if (/\/\/# sourceMappingURL=/.test(bundle)) problems.push('contains a sourceMappingURL comment');
if (problems.length > 0) {
  fail(`the bundle is not human-readable (${problems.join(', ')}). WordPress.org requires readable code.`);
}

// --- 3. write them ------------------------------------------------------------

const same = (path, data) => existsSync(path) && Buffer.compare(readFileSync(path), Buffer.from(data)) === 0;
const bundleSame = same(BUNDLE, bundleBytes);
const pdfChanged = Object.entries(pdfFiles).filter(([target, data]) => !same(target, data));
if (bundleSame && pdfChanged.length === 0) {
  console.log(`The bundled widget already matches ${tag}. Nothing to update.`);
  output('changed', 'false');
  process.exit(0);
}
if (!bundleSame) {
  writeFileSync(BUNDLE, bundleBytes);
  console.log(`Wrote ${BUNDLE} from ${tag} (${lines.length} lines).`);
}
for (const [target, data] of pdfChanged) {
  mkdirSync(dirname(target), { recursive: true });
  writeFileSync(target, data);
  console.log(`Wrote ${target} from ${tag}.`);
}

// --- 4. the plugin version ----------------------------------------------------

const parse = (v) => (/^\d+\.\d+\.\d+$/.test(v) ? v.split('.').map(Number) : null);
const compare = (a, b) => a[0] - b[0] || a[1] - b[1] || a[2] - b[2];

let plugin = readFileSync(PLUGIN_FILE, 'utf8');
const headerMatch = plugin.match(/^ \* Version:\s*(\S+)/m);
if (!headerMatch || !parse(headerMatch[1])) fail(`cannot read the Version header in ${PLUGIN_FILE}`);
const headerVersion = headerMatch[1];

let published;
const listed = option('--published');
if (listed !== undefined) {
  published = listed.split(',').map((v) => v.trim()).filter(Boolean);
} else {
  try {
    published = execFileSync('svn', ['ls', SVN_TAGS], { encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] })
      .split('\n')
      .map((v) => v.replace(/\/$/, '').trim())
      .filter(Boolean);
  } catch (err) {
    fail(`cannot read the published versions from ${SVN_TAGS}: ${String(err.stderr || err.message).trim()}`);
  }
}
const newest = published.map(parse).filter(Boolean).sort(compare).at(-1) ?? null;
const header = parse(headerVersion);

let target = headerVersion;
if (newest && compare(header, newest) <= 0) {
  target = `${newest[0]}.${newest[1]}.${newest[2] + 1}`;
}
console.log(
  `Plugin version: header ${headerVersion}, newest published ${newest ? newest.join('.') : 'none'}, new version ${target}` +
    (target === headerVersion ? ' (not published yet, kept)' : ' (bumped)')
);

if (target !== headerVersion) {
  plugin = plugin.replace(/^( \* Version:\s*)\S+/m, `$1${target}`);
  const constant = /(define\('ETHORA_PLUGIN_VERSION',\s*')[^']+('\);)/;
  if (!constant.test(plugin)) fail(`cannot find ETHORA_PLUGIN_VERSION in ${PLUGIN_FILE}`);
  plugin = plugin.replace(constant, `$1${target}$2`);
  // The docblock tag repeats the version; keep it in step when it is there.
  plugin = plugin.replace(/^( \* @version\s+)\S+/m, `$1${target}`);
  writeFileSync(PLUGIN_FILE, plugin);
}

// --- 5. readme: Stable tag and changelog --------------------------------------

let readme = readFileSync(README, 'utf8');
if (!/^Stable tag:\s*\S+/m.test(readme)) fail(`cannot find Stable tag in ${README}`);
readme = readme.replace(/^(Stable tag:\s*)\S+/m, `$1${target}`);

const bullet = `* Updated: bundled widget rebuilt from widget release ${tag}.`;
const changelogStart = '== Changelog ==\n\n';
if (!readme.includes(changelogStart)) fail(`cannot find the Changelog section in ${README}`);
const heading = `= ${target} =\n`;
const headingAt = readme.indexOf(heading, readme.indexOf(changelogStart));
if (headingAt >= 0) {
  if (!readme.includes(bullet)) {
    const insertAt = headingAt + heading.length;
    readme = readme.slice(0, insertAt) + bullet + '\n' + readme.slice(insertAt);
  }
} else {
  readme = readme.replace(changelogStart, `${changelogStart}${heading}${bullet}\n\n`);
}
writeFileSync(README, readme);

console.log(`Updated ${README} and ${PLUGIN_FILE}.`);
output('changed', 'true');
output('version', target);
