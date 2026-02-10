<?php
/**
 * Template du formulaire de connexion
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<section class="user__connect" 
         id="user__connect" 
         data-logged="<?php echo get_auth_manager()->isLoggedIn() ? 'true' : 'false'; ?>" 
         data-redirect="<?php echo get_permalink(157); ?>" 
         style="display: none;">
    
<?php if(!$is_logged): ?>      
    <!-- FORMULAIRE DE CONNEXION -->
    <form class="user__form" id="user-connexion-form">
        <h2 class="title user__title">Connexion</h2>
        <input class="user__input" type="email" name="mail" placeholder="Email" required>
        <input class="user__input" type="password" name="password" placeholder="Mot de passe" required>
        <button class="btn btn-cta user__btn" type="submit">Se connecter</button>
        <a href="#" class="user__register-link" id="toggle-register">Pas encore de compte ? Inscrivez-vous</a>
        <div id="user-connexion-message"></div>
    </form>

    <!-- FORMULAIRE D'INSCRIPTION -->
    <form class="user__form" id="user-inscription-form" style="display: none;">
        <h2 class="title user__title">Inscription</h2>
        <input class="user__input" type="text" name="name" placeholder="Prénom" required>
        <input class="user__input" type="text" name="lastname" placeholder="Nom" required>
        <input class="user__input" type="email" name="mail" placeholder="Email" required>
        <input class="user__input" type="password" name="password" placeholder="Mot de passe" required>
        <input class="user__input" type="password" name="confirm-password" placeholder="Confirmer le mot de passe" required>
        <button class="btn btn-cta user__btn" type="submit">S'inscrire</button>
        <a href="#" class="user__register-link" id="toggle-login">Déjà un compte ? Connectez-vous</a>
        <div id="user-inscription-message"></div>
    </form>
    <?php endif; ?>
</section>

