<!DOCTYPE html>
<html>
<head>
<title>Gemini Test</title>
</head>
<body>

<h2>Preguntar a Gemini</h2>

<input type="text" id="mensaje" placeholder="Escribe algo">
<button onclick="enviar()">Preguntar</button>

<pre id="respuesta"></pre>

<script>
function enviar() {

    let mensaje = document.getElementById("mensaje").value;

    fetch("/gemini", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            mensaje: mensaje
        })
    })
    .then(res => res.json())
    .then(data => {

        let texto = data.candidates[0].content.parts[0].text;

        document.getElementById("respuesta").innerText = texto;
    });

}
</script>

</body>
</html>