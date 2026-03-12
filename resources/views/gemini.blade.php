<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Deskcir AI</title>

<style>

body{
    font-family: Arial;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.chat-container{
    width:600px;
    background:#111827;
    border-radius:12px;
    box-shadow:0 10px 40px rgba(0,0,0,0.5);
    padding:20px;
}

.chat-title{
    font-size:22px;
    margin-bottom:15px;
}

.messages{
    height:300px;
    overflow:auto;
    margin-bottom:15px;
}

.message-user{
    text-align:right;
    margin:10px 0;
}

.message-ai{
    text-align:left;
    margin:10px 0;
}

.bubble{
    display:inline-block;
    padding:10px 15px;
    border-radius:10px;
    max-width:80%;
}

.user{
    background:#3b82f6;
}

.ai{
    background:#374151;
}

.input-area{
    display:flex;
}

input{
    flex:1;
    padding:10px;
    border:none;
    border-radius:8px;
    margin-right:10px;
}

button{
    padding:10px 20px;
    border:none;
    background:#10b981;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#059669;
}

</style>

</head>

<body>

<div class="chat-container">

<div class="chat-title">Deskcir AI 🤖</div>

<div class="messages" id="messages"></div>

<div class="input-area">

<input id="mensaje" placeholder="Pregúntale algo a la IA">

<button onclick="enviar()">Enviar</button>

</div>

</div>

<script>

function enviar(){

let mensaje = document.getElementById("mensaje").value;

let chat = document.getElementById("messages");

chat.innerHTML += `
<div class="message-user">
<div class="bubble user">${mensaje}</div>
</div>
`;

fetch("/gemini",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},
body:JSON.stringify({
mensaje:mensaje
})
})
.then(res=>res.json())
.then(data=>{

let texto = data.candidates[0].content.parts[0].text;

chat.innerHTML += `
<div class="message-ai">
<div class="bubble ai">${texto}</div>
</div>
`;

chat.scrollTop = chat.scrollHeight;

});

}

</script>

</body>
</html>