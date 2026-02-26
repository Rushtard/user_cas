<?php
// Si Nextcloud charge ce fichier en mode legacy, on sort.
// Il doit être exécuté uniquement via Application::boot().
if (!defined('USER_CAS_ENFORCE_BOOTED')) {
	return;
}
