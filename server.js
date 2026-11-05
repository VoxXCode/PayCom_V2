// Node.js Native HTTP Server untuk PayCom PC Builder
// Berjalan langsung tanpa perlu menginstal dependensi eksternal npm

import http from "node:http";
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = process.env.PORT || 3000;

const MIME_TYPES = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "application/javascript; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".png": "image/png",
  ".jpg": "image/jpeg",
  ".svg": "image/svg+xml",
  ".ico": "image/x-icon"
};

const server = http.createServer((req, res) => {
  const parsedUrl = new URL(req.url, `http://${req.headers.host}`);
  let pathname = parsedUrl.pathname;

  // Endpoint Mock API Harga Gratis (FR2)
  if (pathname === "/api/price-estimate") {
    const query = parsedUrl.searchParams.get("query") || "";
    res.writeHead(200, {
      "Content-Type": "application/json; charset=utf-8",
      "Access-Control-Allow-Origin": "*"
    });
    res.end(
      JSON.stringify({
        status: "success",
        query,
        timestamp: new Date().toISOString(),
        note: "API Penarikan Estimasi Harga Online Terintegrasi"
      })
    );
    return;
  }

  // Static File Serving
  if (pathname === "/") {
    pathname = "/index.html";
  }

  const safePath = path.normalize(pathname).replace(/^(\.\.[\/\\])+/, "");
  const filePath = path.join(__dirname, safePath);

  fs.stat(filePath, (err, stats) => {
    if (err || !stats.isFile()) {
      res.writeHead(404, { "Content-Type": "text/plain; charset=utf-8" });
      res.end("404 Not Found: Berkas tidak ditemukan.");
      return;
    }

    const ext = path.extname(filePath).toLowerCase();
    const contentType = MIME_TYPES[ext] || "application/octet-stream";

    res.writeHead(200, { "Content-Type": contentType });
    const stream = fs.createReadStream(filePath);
    stream.pipe(res);
  });
});

server.listen(PORT, () => {
  console.log(`================================================`);
  console.log(`  PayCom PC Builder Server Aktif!               `);
  console.log(`  URL: http://localhost:${PORT}                 `);
  console.log(`  Buka browser Anda dan akses tautan di atas.   `);
  console.log(`================================================`);
});

