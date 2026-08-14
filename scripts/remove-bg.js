/**
 * Script simple para eliminar píxeles casi negros y hacerlos transparentes.
 * Requisitos: `npm install jimp --save-dev`
 * Uso: coloca tu imagen en `src/assets/imgs/upload_logo.*` y ajusta `input`.
 */
const Jimp = require("jimp");
const fs = require("fs");

const input = "src/assets/imgs/upload_logo.png"; // sube aquí tu imagen
const output = "src/assets/imgs/logo.png"; // resultado final

if (!fs.existsSync(input)) {
  console.error(`No se encontró ${input}. Por favor sube tu imagen en esa ruta.`);
  process.exit(1);
}

Jimp.read(input)
  .then((image) => {
    image.rgba(true);
    const width = image.bitmap.width;
    const height = image.bitmap.height;

    image.scan(0, 0, width, height, function (x, y, idx) {
      const r = this.bitmap.data[idx + 0];
      const g = this.bitmap.data[idx + 1];
      const b = this.bitmap.data[idx + 2];
      // Si el pixel es muy oscuro (cerca de negro), hacerlo transparente
      if (r < 40 && g < 40 && b < 40) {
        this.bitmap.data[idx + 3] = 0;
      }
    });

    return image.writeAsync(output);
  })
  .then(() => {
    console.log(`Procesado y guardado en ${output}`);
  })
  .catch((err) => {
    console.error("Error procesando imagen:", err);
  });
