import fs from "fs";
import { GoogleGenerativeAI } from "@google/generative-ai";

const apiKey = process.env.GEMINI_API_KEY;

if (!apiKey) {
  throw new Error("Missing GEMINI_API_KEY environment variable.");
}

const genAI = new GoogleGenerativeAI(apiKey);

const model = genAI.getGenerativeModel({
  model: process.env.GEMINI_MODEL || "gemini-2.5-flash"
});

async function analizarProyecto(pregunta) {
  const rutas = fs.readFileSync("routes/web.php", "utf8");

  const logs = fs.existsSync("storage/logs/laravel.log")
    ? fs.readFileSync("storage/logs/laravel.log", "utf8").slice(-3000)
    : "No hay logs";

  const prompt = `
Eres un experto en Laravel.

CONTEXTO DEL PROYECTO:

Rutas:
${rutas}

Logs:
${logs}

Pregunta del desarrollador:
${pregunta}
`;

  const result = await model.generateContent(prompt);

  console.log(result.response.text());
}

analizarProyecto("Hay errores en mis rutas?");
