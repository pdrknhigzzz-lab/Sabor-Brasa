
const formulario = document.querySelector(".caixa-login form");

if (formulario) {

    const campos = formulario.querySelectorAll("input");

    campos.forEach(function(campo) {

        // Criar mensagem de erro automaticamente
        let mensagem = campo.nextElementSibling;

        if (!mensagem || mensagem.tagName !== "SMALL") {
            mensagem = document.createElement("small");
            campo.insertAdjacentElement("afterend", mensagem);
        }

        campo.addEventListener("input", function() {

            if (campo.value.trim() !== "") {

                campo.style.border = "";
                campo.style.backgroundColor = "";
                mensagem.textContent = "";

            }

        });

    });

    formulario.addEventListener("submit", function(event) {

        let valido = true;

        campos.forEach(function(campo) {

            const mensagem = campo.nextElementSibling;

            if (campo.value.trim() === "") {

                event.preventDefault();
                valido = false;

                campo.style.border = "2px solid red";
                campo.style.backgroundColor = "#fff0f0";

                mensagem.textContent = "Preencha este campo!";
                mensagem.style.color = "red";
                mensagem.style.fontSize = "12px";
                mensagem.style.display = "block";
                mensagem.style.marginBottom = "10px";

            } else {

                campo.style.border = "";
                campo.style.backgroundColor = "";
                mensagem.textContent = "";

            }

        });

        // Verificar se é cadastro
        const senha = formulario.querySelector('[name="senha"]');
        const confirmar = formulario.querySelector('[name="confirmar_senha"]');

        if (senha && confirmar && senha.value.trim() !== "" && confirmar.value.trim() !== "") {

            if (senha.value !== confirmar.value) {

                event.preventDefault();
                valido = false;

                confirmar.style.border = "2px solid red";
                confirmar.style.backgroundColor = "#fff0f0";

                const mensagem = confirmar.nextElementSibling;

                mensagem.textContent = "As senhas não são iguais!";
                mensagem.style.color = "red";
                mensagem.style.fontSize = "12px";
                mensagem.style.display = "block";

            }

        }

    });

}

