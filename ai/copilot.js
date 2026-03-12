import fs from "fs";
import { GoogleGenerativeAI } from "@google/generative-ai";

const genAI = new GoogleGenerativeAI("AIzaSyD_cH2-P7Nx1ModBprNTvrUFjU_Ssp-5R8");

const model = genAI.getGenerativeModel({
  model: "gemini-2.0-flash"
});

async function analizarProyecto(pregunta){

    const rutas = fs.readFileSync("routes/web.php","utf8");

    const logs = fs.existsSync("storage/logs/laravel.log")
        ? fs.readFileSync("storage/logs/laravel.log","utf8").slice(-3000)
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

analizarProyecto("¿Hay errores en mis rutas?");