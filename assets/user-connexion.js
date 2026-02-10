// Toggle du panneau de connexion
const userBtn = document.querySelector('#user__btn');
const userConnect = document.querySelector('#user__connect');
let isBtnActive = false;

// Test AJAX Debug au chargement
if (typeof user_connexion !== 'undefined') {
    console.log('user_connexion loaded:', user_connexion);
    // Test de l'endpoint de debug
    fetch(user_connexion.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'user_connexion_debug'
        })
    })
    .then(response => response.text())
    .then(text => {
        console.log('Debug response:', text);
    })
    .catch(error => {
        console.error('Debug AJAX échoué:', error);
    });
} else {
    console.error('user_connexion non défini - check wp_localize_script');
}
// Gestion du clic sur le bouton de connexion
if (userBtn) {
    userBtn.addEventListener('click', (e) => {
        if (!userConnect) {
            console.warn('Élément #user__connect introuvable.');
            return;
        }

        const isLogged = userConnect.getAttribute('data-logged') === 'true';
        const redirectUrl = userConnect.getAttribute('data-redirect');

        if(isLogged && redirectUrl) {
            window.location.href = redirectUrl;
            return;
        }

        isBtnActive = !isBtnActive;
        userConnect.style.display = isBtnActive ? 'flex' : 'none';
    });
} else {
    console.warn('Élément #user__btn introuvable.');
}

// Toggle entre Connexion et Inscription
const toggleRegisterLink = document.querySelector('#toggle-register');
const toggleLoginLink = document.querySelector('#toggle-login');
const loginForm = document.querySelector('#user-connexion-form');
const registerForm = document.querySelector('#user-inscription-form');
// Afficher le formulaire de connexion et masquer celui d'inscription
if (toggleRegisterLink) {
    toggleRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'flex';
    });
}
// Afficher le formulaire de connexion et masquer celui d'inscription
if (toggleLoginLink) {
    toggleLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'flex';
        registerForm.style.display = 'none';
    });
}

// ===== GESTION DE LA CONNEXION VIA AJAX =====
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const mail = this.querySelector('input[name="mail"]').value;
        const password = this.querySelector('input[name="password"]').value;
        const messageDiv = document.querySelector('#user-connexion-message');
        const redirectUrl = userConnect.getAttribute('data-redirect');

        // Vérifier que user_connexion est disponible
        if (typeof user_connexion === 'undefined') {
            console.error('Variable user_connexion non trouvée.');
            messageDiv.innerHTML = '<p style="color: red;">Variable user_connexion manquante.</p>';
            return;
        }
        // Afficher les données envoyées
        console.log('Données AJAX:', {
            action: 'user_connexion_login',
            nonce: user_connexion.nonce,
            mail: mail,
            password: '****'
        });

        // Envoyer la requête AJAX
        fetch(user_connexion.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'user_connexion_login',
                nonce: user_connexion.nonce,
                mail: mail,
                password: password
            })
        })
        .then(response => {
            console.log('Réponse brute:', response);
            return response.json();
        })
        .then(data => {
            console.log('Réponse JSON:', data);
            if (data.success) {
                messageDiv.innerHTML = '<p style="color: green;">' + data.data.message + '</p>';
                loginForm.reset();
                // Optionnel : rediriger après connexion
                setTimeout(() => {
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        // Sécurité au cas où l'attribut serait manquant
                        location.reload();
                    }
                }, 1000);
            } else {
                messageDiv.innerHTML = '<p style="color: red;">' + data.data.message + '</p>';
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            messageDiv.innerHTML = '<p style="color: red;">Erreur serveur. Essayez plus tard.</p>';
        });
    });
}

// ===== GESTION DE L'INSCRIPTION VIA AJAX =====
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const name = this.querySelector('input[name="name"]').value;
        const lastname = this.querySelector('input[name="lastname"]').value;
        const mail = this.querySelector('input[name="mail"]').value;
        const password = this.querySelector('input[name="password"]').value;
        const confirmPassword = this.querySelector('input[name="confirm-password"]').value;
        const messageDiv = document.querySelector('#user-inscription-message');

        // Valider les mots de passe
        if (password !== confirmPassword) {
            messageDiv.innerHTML = '<p style="color: red;">Les mots de passe ne correspondent pas.</p>';
            return;
        }

        // Vérifier que user_connexion est disponible
        if (typeof user_connexion === 'undefined') {
            console.error('Variable user_connexion non trouvée.');
            return;
        }

        // Envoyer la requête AJAX
        fetch(user_connexion.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'user_connexion_register',
                nonce: user_connexion.nonce,
                name: name,
                lastname: lastname,
                mail: mail,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = '<p style="color: green;">' + data.data.message + '</p>';
                registerForm.reset();
                // Rediriger vers la connexion après 2s
                setTimeout(() => {
                    loginForm.style.display = 'flex';
                    registerForm.style.display = 'none';
                    messageDiv.innerHTML = '';
                }, 2000);
            } else {
                messageDiv.innerHTML = '<p style="color: red;">' + data.data.message + '</p>';
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            messageDiv.innerHTML = '<p style="color: red;">Erreur serveur. Essayez plus tard.</p>';
        });
    });
}

// ===== GESTION DE LA DECONNEXION VIA AJAX =====
const logoutLink = document.querySelector('#user-logout-link');
//Support de lien pour la déconnexion
if(logoutLink) {
    logoutLink.addEventListener('click', function(e) {
        e.preventDefault();
        handleLogout();
    })
}
//Fonction de déconnexion
function handleLogout() {
    if(typeof user_connexion === 'undefined') {
        console.error('Variable user_connexion non trouvée.');
        return;
    }
    // Envoyer la requête AJAX
    fetch(user_connexion.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action : 'user_connexion_logout',
            nonce : user_connexion.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            console.log('Déconnexion réussie');
            // Redirection vers front-page
            window.location.href = '/laPlaceRouge/';
        } else {
            const errorMessage = data.data.message || 'Erreur inconnue';
            console.error('Erreur lors de la déconnexion:', errorMessage);
            alert('Erreur lors de la déconnexion. Essayez à nouveau.');}
    })
    .catch(error => {
        console.error('ERREUR AJAX:', error);
        alert('Erreur réseau lors de la déconnexion. Essayez à nouveau.');
    });
}