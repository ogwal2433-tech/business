const fs = require('fs');
const path = require('path');
const zlib = require('zlib');

const SIZE = 512;
const buf = Buffer.alloc(SIZE * SIZE * 4);

for (let y = 0; y < SIZE; y++) {
  for (let x = 0; x < SIZE; x++) {
    const idx = (y * SIZE + x) * 4;
    const cx = SIZE / 2, cy = SIZE / 2;
    const dist = Math.sqrt((x - cx) ** 2 + (y - cy) ** 2);
    const ratio = Math.min(dist / (SIZE / 2), 1);
    const r1 = 30, g1 = 58, b1 = 138;
    const r2 = 37, g2 = 99, b2 = 235;
    buf[idx] = Math.round(r1 + (r2 - r1) * ratio);
    buf[idx+1] = Math.round(g1 + (g2 - g1) * ratio);
    buf[idx+2] = Math.round(b1 + (b2 - b1) * ratio);
    buf[idx+3] = 255;
  }
}

const circleR = SIZE * 0.28;
const cx = SIZE / 2, cy = SIZE / 2;
for (let y = 0; y < SIZE; y++) {
  for (let x = 0; x < SIZE; x++) {
    const idx = (y * SIZE + x) * 4;
    const dist = Math.sqrt((x - cx) ** 2 + (y - cy) ** 2);
    if (dist < circleR) {
      const busW = circleR * 1.5;
      const busH = circleR * 0.8;
      const bx = cx - busW / 2;
      const by = cy - busH / 2;
      if (x > bx + 4 && x < bx + busW - 4 && y > by + 4 && y < by + busH - 4) {
        buf[idx] = 255; buf[idx+1] = 255; buf[idx+2] = 255;
      }
    }
  }
}

const rawData = Buffer.alloc(SIZE * (1 + SIZE * 4));
for (let y = 0; y < SIZE; y++) {
  rawData[y * (1 + SIZE * 4)] = 0;
  for (let x = 0; x < SIZE; x++) {
    const srcIdx = (y * SIZE + x) * 4;
    const dstIdx = y * (1 + SIZE * 4) + 1 + x * 4;
    rawData[dstIdx] = buf[srcIdx];
    rawData[dstIdx+1] = buf[srcIdx+1];
    rawData[dstIdx+2] = buf[srcIdx+2];
    rawData[dstIdx+3] = buf[srcIdx+3];
  }
}

const compressed = zlib.deflateSync(rawData);

function crc32(data) {
  let crc = 0xFFFFFFFF;
  const table = new Int32Array(256);
  for (let i = 0; i < 256; i++) {
    let c = i;
    for (let j = 0; j < 8; j++) {
      c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
    }
    table[i] = c;
  }
  for (let i = 0; i < data.length; i++) {
    crc = table[(crc ^ data[i]) & 0xFF] ^ (crc >>> 8);
  }
  return (crc ^ 0xFFFFFFFF) >>> 0;
}

function chunk(type, data) {
  const len = Buffer.alloc(4);
  len.writeUInt32BE(data.length);
  const typeB = Buffer.from(type, 'ascii');
  const crcData = Buffer.concat([typeB, data]);
  const crcB = Buffer.alloc(4);
  crcB.writeUInt32BE(crc32(crcData));
  return Buffer.concat([len, typeB, data, crcB]);
}

const signature = Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]);
const ihdr = Buffer.alloc(13);
ihdr.writeUInt32BE(SIZE, 0);
ihdr.writeUInt32BE(SIZE, 4);
ihdr[8] = 8; ihdr[9] = 6; ihdr[10] = 0; ihdr[11] = 0; ihdr[12] = 0;
const iend = Buffer.from([0, 0, 0, 0, 73, 69, 78, 68, 174, 66, 96, 130]);

const png = Buffer.concat([
  signature,
  chunk('IHDR', ihdr),
  chunk('IDAT', compressed),
  iend
]);

const outPath = path.join(__dirname, 'icons', 'icon.png');
fs.writeFileSync(outPath, png);
console.log('Created icon: ' + outPath + ' (' + png.length + ' bytes)');
