// Renders the WordPress.org listing banners (wp-assets/banner-*.png) from the
// official Ethora mark. No network access is needed.
//
// Usage: node scripts/make-wp-banners.mjs <path-to-playwright-core/index.mjs> <path-to-ethora-logo-white.svg>
// Needs an installed Google Chrome (playwright channel "chrome").
import fs from 'node:fs'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const [pw, logoPath] = process.argv.slice(2)
if (!pw || !logoPath) {
  console.error('usage: node scripts/make-wp-banners.mjs <playwright-core/index.mjs> <ethora-logo-white.svg>')
  process.exit(2)
}
const { chromium } = await import(pw)
const out = path.join(path.dirname(fileURLToPath(import.meta.url)), '..', 'wp-assets')
const mark = fs.readFileSync(logoPath, 'utf8').replace(/<svg[^>]*>/, '<svg viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">')

const html = (w, h) => `<!doctype html><meta charset="utf-8"><style>
*{box-sizing:border-box;margin:0}
body{width:${w}px;height:${h}px;overflow:hidden;font-family:-apple-system,"Helvetica Neue",Arial,sans-serif;color:#fff;
 background:linear-gradient(120deg,#061430 0%,#0a204e 100%);position:relative}
.bar{position:absolute;left:0;top:0;bottom:0;width:${Math.round(w / 180)}px;background:#0052cd}
.row{position:absolute;left:${Math.round(w * 0.06)}px;top:0;bottom:0;display:flex;align-items:center;gap:${Math.round(h * 0.09)}px}
.mark{width:${Math.round(h * 0.4)}px;height:${Math.round(h * 0.4)}px;flex:none}
.mark svg{width:100%;height:100%}
h1{font-size:${Math.round(h * 0.17)}px;font-weight:700;letter-spacing:-0.01em;line-height:1.1}
p{margin-top:${Math.round(h * 0.04)}px;font-size:${Math.round(h * 0.07)}px;color:#aabce0}
.sub{margin-top:${Math.round(h * 0.025)}px;font-size:${Math.round(h * 0.052)}px;color:#7f97c9}
.ring{position:absolute;right:${Math.round(w * -0.04)}px;top:50%;width:${Math.round(h * 1.0)}px;height:${Math.round(h * 1.0)}px;margin-top:${Math.round(-h * 0.5)}px;border:3px solid #16346e;border-radius:${Math.round(h * 0.26)}px;transform:rotate(45deg) scale(.82)}
</style><div class="bar"></div><div class="ring"></div>
<div class="row"><div class="mark">${mark}</div><div><h1>Ethora AI Chatbot</h1><p>AI chatbot and live chat for WordPress</p><div class="sub">Trained on your own website and documents</div></div></div>`

const browser = await chromium.launch({ channel: 'chrome', headless: true })
for (const [w, h] of [[1544, 500], [772, 250]]) {
  const page = await browser.newPage({ viewport: { width: w, height: h } })
  await page.setContent(html(w, h))
  await page.screenshot({ path: path.join(out, `banner-${w}x${h}.png`) })
  await page.close()
}
await browser.close()
