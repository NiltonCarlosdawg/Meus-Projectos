document.addEventListener('DOMContentLoaded', () => {
    
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    const userGreetingElement = document.getElementById('userGreeting');
    const logoutButton = document.getElementById('logoutButton');
    const loginLink = document.getElementById('loginLink');
    const signupLink = document.getElementById('signupLink');
    const areaPacienteLink = document.querySelector('a[href="#areaPaciente"]'); 
    const consultasOnlineLink = document.querySelector('a[href="#consultas"]');


    
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true' || false;
            navToggle.setAttribute('aria-expanded', !isExpanded);
            mainNav.classList.toggle('active');
            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }

    
    function checkLoginStatus() {
        return localStorage.getItem('loggedInUser');
    }

    function updateNavForAuthState() {
        const loggedInUser = checkLoginStatus();
        const body = document.body;

        if (loggedInUser) {
            if (userGreetingElement) userGreetingElement.textContent = `Olá, ${loggedInUser}!`;
            if (userGreetingElement) userGreetingElement.style.display = 'block';
            if (logoutButton) logoutButton.style.display = 'block';
            if (loginLink) loginLink.style.display = 'none';
            if (signupLink) signupLink.style.display = 'none';
            if (areaPacienteLink) areaPacienteLink.parentElement.style.display = 'list-item'; 
            if (consultasOnlineLink) consultasOnlineLink.parentElement.style.display = 'list-item'; 

            if (body.classList.contains('home-page')) { 
                 const areaPacienteSection = document.getElementById('areaPaciente');
                 const consultasSection = document.getElementById('consultas');
                 if(areaPacienteSection) areaPacienteSection.classList.remove('restricted');
                 if(consultasSection) consultasSection.classList.remove('restricted');
            }

        } else {
            if (userGreetingElement) userGreetingElement.style.display = 'none';
            if (logoutButton) logoutButton.style.display = 'none';
            if (loginLink) loginLink.style.display = 'block';
            if (signupLink) signupLink.style.display = 'block';

            
            if (areaPacienteLink) areaPacienteLink.parentElement.style.display = 'none';
            if (consultasOnlineLink) consultasOnlineLink.parentElement.style.display = 'none';

            if (body.classList.contains('home-page')) { 
                const areaPacienteSection = document.getElementById('areaPaciente');
                const consultasSection = document.getElementById('consultas');

                if(areaPacienteSection) {
                    areaPacienteSection.classList.add('restricted');
                    areaPacienteSection.innerHTML = `
                        <h2 class="section-title">Minha Área</h2>
                        <p class="restricted-message">
                            <i class="fas fa-lock"></i> Você precisa estar logado para acessar esta área.
                            <a href="login.html" class="btn btn-primary btn-sm">Fazer Login</a>
                        </p>`;
                }
                if(consultasSection) {
                     consultasSection.classList.add('restricted');
                     const btnVideo = document.getElementById('btnVideo');
                     if(btnVideo) btnVideo.style.display = 'none'; 
                     const videoArea = document.getElementById('videoArea');
                     if(videoArea) videoArea.style.display = 'none';

                     const pMessage = consultasSection.querySelector('p');
                     if (pMessage) {
                        pMessage.innerHTML = `
                        <i class="fas fa-lock"></i> Você precisa estar logado para iniciar uma consulta online.
                        <a href="login.html" class="btn btn-primary btn-sm">Fazer Login</a>`;
                     }
                }
            }
        }
    }

    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('loggedInUser');
            localStorage.removeItem('userEmail'); 
            if (document.body.classList.contains('home-page')) {
                updateNavForAuthState(); 
            } else {
                window.location.href = 'login.html'; 
            }
        });
    }

    
    const formLogin = document.getElementById('formLogin');
    if (formLogin) {
        formLogin.addEventListener('submit', function(event) {
            event.preventDefault();
            const email = document.getElementById('emailLogin').value;
            const password = document.getElementById('senhaLogin').value;
            const loginError = document.getElementById('loginError');

           
            const storedUserEmail = localStorage.getItem('userEmail');
            const storedUserPassword = localStorage.getItem('userPassword'); 

            if (email === storedUserEmail && password === storedUserPassword) {
                
                const userName = localStorage.getItem('userName') || email.split('@')[0]; 
                localStorage.setItem('loggedInUser', userName);
                window.location.href = 'code.html'; 
            } else if (email === "teste@teste.com" && password === "123") { 
                localStorage.setItem('loggedInUser', "Usuário Teste");
                localStorage.setItem('userEmail', email); 
                window.location.href = 'code.html';
            }
            else {
                loginError.textContent = 'E-mail ou senha inválidos. Tente novamente.';
                loginError.className = 'form-feedback error show';
                setTimeout(() => { loginError.className = 'form-feedback error'; }, 5000);
            }
        });
    }

    
    const formSignup = document.getElementById('formSignup');
    if (formSignup) {
        formSignup.addEventListener('submit', function(event) {
            event.preventDefault();
            const nome = document.getElementById('nomeSignup').value;
            const email = document.getElementById('emailSignup').value;
            const senha = document.getElementById('senhaSignup').value;
            const confirmarSenha = document.getElementById('confirmarSenhaSignup').value;
            const signupMessage = document.getElementById('signupMessage');

            if (senha !== confirmarSenha) {
                signupMessage.textContent = 'As senhas não coincidem!';
                signupMessage.className = 'form-feedback error show';
                setTimeout(() => { signupMessage.className = 'form-feedback error'; }, 3000);
                return;
            }

            
            localStorage.setItem('userName', nome);
            localStorage.setItem('userEmail', email);
            localStorage.setItem('userPassword', senha); 

            signupMessage.textContent = 'Cadastro realizado com sucesso! Redirecionando para login...';
            signupMessage.className = 'form-feedback success show';
            formSignup.reset();
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        });
    }

    
    const formConsulta = document.getElementById('formConsulta');
    if (formConsulta) {
        formConsulta.addEventListener('submit', function(event) {
            event.preventDefault();
            const confirmacaoDiv = document.getElementById('confirmacaoConsulta');
            if (checkLoginStatus()) {
                confirmacaoDiv.textContent = 'Consulta agendada com sucesso! (Esta é uma mensagem de demonstração)';
                confirmacaoDiv.className = 'form-feedback success show';
                formConsulta.reset();
                setTimeout(() => {
                    confirmacaoDiv.className = 'form-feedback success';
                }, 5000);
            } else {
                confirmacaoDiv.textContent = 'Você precisa estar logado para marcar uma consulta.';
                confirmacaoDiv.className = 'form-feedback error show';
                 setTimeout(() => {
                    confirmacaoDiv.className = 'form-feedback error';
                    window.location.href = 'login.html';
                }, 3000);
            }
        });
    }

    
    const formContato = document.getElementById('formContato');
    if (formContato) {
        formContato.addEventListener('submit', function(e) {
            e.preventDefault();
           
            alert('Mensagem enviada com sucesso! (Demonstração)');
            formContato.reset();
        });
    }

   
    updateNavForAuthState();


    const btnVideo = document.getElementById('btnVideo');
    const videoArea = document.getElementById('videoArea');
    if (btnVideo && videoArea) {
        btnVideo.addEventListener('click', () => {
            if (checkLoginStatus()) {
                videoArea.style.display = 'block';
                btnVideo.style.display = 'none';
            } else {
                alert('Você precisa estar logado para iniciar uma videochamada.');
                window.location.href = 'login.html';
            }
        });
    }
});