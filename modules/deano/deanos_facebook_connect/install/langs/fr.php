<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
	$sLangCategory = 'Deanos Facebook Connect';


    $aLangContent = array
    (
        '_dbcs_facebook'                         => 'Deanos FBC',
        '_dbcs_facebook_settings'                => 'Réglages Facebook connect',
        '_dbcs_facebook_information'             => 'Information Facebook connect',
        '_dbcs_facebook_information_block'       => 'NOTE: Facebook connect nécessite une application Facebook pour fonctionner. Pour en créer une, ou pour obtenir les clés de votre application existante, rendez-vous sur <a href="http://www.facebook.com/developers/">http://www.facebook.com/developers/</a><br><br>Pour vous assurez que vous avez correctement paramétré votre application pour utiliser Facebook connect, utilisez la capture d\'écran disponible ici <a href="http://www.deanbassett.com/images/facebook_screenshots/">http://www.deanbassett.com/images/facebook_screenshots/</a><br><br>Après avoir suivi ces étapes, copiez-collez l\'ID et la clé secrète de l\'application Facebook ci-dessous :<br><br>Un ID de connexion Dolphin sera sélectionné parmi les champs Facebook, dans l\'ordre affiché ci-dessous. Le premier est utilisé par défaut. S\'il est déjà utilisé, le suivant sera alors sélectionné.<br>&nbsp;',
        '_dbcs_facebook_error_occured'           => 'Désolé, une erreur est survenue',
        '_dbcs_facebook_profile_exist'           => 'Désolé, mais le profil {0} existe déjà.',
        '_dbcs_facebook_profile_not_defined'     => 'Désolé, mais le profil ne peut pas être déterminé',
        '_dbcs_facebook_profile_error_info'      => 'Désolé. Impossible de définir vos informations de profil',
        '_dbcs_facebook_profile_error_api_keys'  => 'Merci de configurer le module dans le panneau d\'administration (impossible de déterminer les clés API Facebook)',
        '_dbcs_fbc_Restore'                    => 'Restaurer Sauvegarde',
        '_dbcs_fbc_Backup'                     => 'Créer Sauvegarde',
        '_dbcs_fb_No Backups Found'            => 'Aucun fichier de Sauvegarde trouvé',
        '_dbcs_fb_Backup Saved'                => 'Sauvegarde enregistrée',
        '_dbcs_fb_Backup Restored'             => 'Sauvegarde restaurée',
        '_dbcs_fb_Backup Deleted'              => 'Sauvegarde supprimée',
        '_FieldCaption_dbcsFacebookProfile_Edit'   => 'ID de Profil Facebook',
        '_FieldCaption_dbcsFacebookProfile_Join'   => 'ID de Profil Facebook',
        '_FieldCaption_dbcsFacebookProfile_View'   => 'ID de Profil Facebook',
        '_FieldCaption_dbcsFacebookProfile_Search'   => 'ID de Profil Facebook',
        '_dbcs_FC_profile_info_required'   => 'Vérification de Profil',
        '_dbcs_FC_profile_info_required_msg'   => 'Il manque des informations nécessaires à la création de votre profil. Merci de les compléter.',
        '_dbcs_FC_profile_info_required_link'   => 'Aller à l\'édition de votre profil',

        '_dbcs_fbc_enter_password'   => 'Entrer votre mot de passe',

        '_dbcs_fbc_pass_msg'   => 'Bienvenue, et merci pour votre inscription. La plupart des informations nécessaires ont été reçues depuis Facebook. Il ne vous reste qu\'à choisir un mot de passe pour vous connecter de façon "classique".<br /><br />Merci d\'indiquer ce mot de passe ci-dessous.',
        '_dbcs_fbc_nickname'   => 'Identifiant',
        '_dbcs_fbc_password'   => 'Mot de passe',
        '_dbcs_fbc_confirm_password'   => 'Confirmer le mot de passe',
        '_dbcs_fbc_email'   => 'Adresse Email',
		'_dbcs_fbc_sex' => 'Sexe',
		'_dbcs_fbc_dob' => 'Date de naissance',
		'_dbcs_fbc_country' => 'Pays',
		'_dbcs_fbc_city' => 'Commune',
		'_dbcs_fbc_zip' => 'Code postal',

        '_dbcs_fbc_submit'   => 'Envoyer',
        '_dbcs_facebook_member_photos'   => 'Photos Facebook',
        '_dbcs_facebook_join_feature_unavailable'   => 'La fonctionnalité de récupération des variables Facebook dans le formulaire d\'inscription de Dolphin n\'est pas disponible pour<br /> la version 7.0.0 de Dolphin.<br /><br /> Cette fonctionnalité n\'est disponible que pour les versions de Dolphin 7.0.1 et supérieures.<br /><br />Merci de désactiver cette fonctionnalité ou de faire une mise à jour vers une version supérieure de Dolphin.',
        '_dbcs_permissions_error' => 'Erreur de Permissions de dossiers.',
        '_dbcs_permissions_error_msg' => 'Les données temporaires doivent être sauvegardées pendant le processus d\'inscription à Facebook. Facebook Connect ne peut pas les stocker car le dossier tmp semble ne pas avoir les bonnes permissions d\'écriture.',
        '_dbcs_fb_ask_import' => 'Souhaitez-vous importer les albums photos de Facebook vers ce site ?',
        '_dbcs_fb_ask_import_header' => 'Importer les Albums',
        '_dbcs_fb_importing_msg' => 'Importation des albums-photos Facebook. Merci de patienter...',
        '_dbcs_fb_importing_header' => 'Importation des Albums',
        '_dbcs_fb_finish_header' => 'Votre compte est créé',
        '_dbcs_fb_finish_msg' => 'Votre compte a été créé. Merci d\'utiliser Facebook Connect.',
        '_dbcs_fb_logon_label' => 'Votre identifiant :',
        '_dbcs_fb_password_label' => 'Votre mot de passe :',
        '_dbcs_fb_ip_banned' => 'Désolé, mais votre IP a été bannie. Le processus d\'inscription a été annulé.',
        '_dbcs_fb_erh' => 'Erreur. Il manque une extension PHP.',
        '_dbcs_fb_erc' => 'Facebook Connect a besoin de l\'extension PHP Curl. Vérifiez votre hébergement pour vous assurer qu\'elle est bien installée.',
        '_dbcs_fb_erj' => 'Facebook Connect a besoin de l\'extension PHP JSON. Vérifiez votre hébergement pour vous assurer qu\'elle est bien installée.',
        '_dbcs_fb_curl_test_failed' => 'Les tests de vérification du bon fonctionnement de Curl ont échoué avec le message suivant : <br /><br />{0}<br /><br />Contactez votre hébergeur.',
        '_dbcs_fb_dns_test_failed' => 'Les serveurs DNS ne semblent pas fonctionner. Impossible de joindre le domaine graph.facebook.com<br /><br /> Contactez votre hébergeur.',
        '_dbcs_fb_min_age_error' => 'Désolé, mais l\'accès à ce site est réservé aux personnes âgées de {0} ans et plus.',
        '_dbcs_fb_invalid_email' => 'Désolé, mais l\'adresse email de {0} obtenue depuis Facebook n\'est pas une adresse valide.<br><br>L\'inscription est annulée. Merci de vérifier votre adresse email sur Facebook et de recommencer',
        '_dbcs_fbc_prompt' => 'Des informations complémentaires sont nécessaires pour compléter votre inscription.',
        '_dbcs_fbc_prompt_nick_msg' => 'Votre pseudo ne peut pas être généré automatiquement. Merci de choisir un pseudonyme pour utiliser le site.',
        '_dbcs_fbc_prompt_email_msg' => 'L\'adresse e-mail obtenue depuis Facebook n\'est pas valide. Merci d\'indiquer une adresse valide pour utiliser nos services.',
        '_dbcs_fbc_default_headline' => '',
        '_dbcs_fbc_default_about_me' => '',
        '_dbcs_fbc_button_text_lg' => 'Connexion avec Facebook',
        '_dbcs_fbc_button_text_sm' => 'Connexion',
        '_dbcs_fbc_button_continue' => 'Continuer',
        '_dbcs_fbc_onemoment' => 'Création de votre compte en cours. Merci de patienter.',
        '_dbcs_fbc_login_popup_text' => 'Merci de patienter.',
	'_dbcs_fbc_profile_type' => 'Type de Profil',
        '_dbcs_fbc_yes' => 'Oui',
        '_dbcs_fbc_no' => 'Non',
		'_dbcs_fbc_profile_photo' => 'profil Photo',
		'_dbcs_fbc_FieldError_City_Mandatory' => 'Vous devez spécifier votre ville',
		'_dbcs_fbc_FieldError_City_Max' => 'S\'il vous plaît, entrez {0} caractères ou moins',
		'_dbcs_fbc_FieldError_City_Min' => 'S\'il vous plaît, entrez {0} caractères ou plus',
		'_dbcs_fbc_FieldError_Country_Mandatory' => 'Vous devez spécifier votre pays',
		'_dbcs_fbc_FieldError_DateOfBirth_Mandatory' => 'Vous devez indiquer votre date de naissance',
		'_dbcs_fbc_FieldError_DateOfBirth_Max' => 'Vous ne pouvez pas joindre le site si vous avez plus de {0} ans',
		'_dbcs_fbc_FieldError_DateOfBirth_Min' => 'Vous ne pouvez pas joindre le site si vous avez moins de {0} ans',
		'_dbcs_fbc_FieldError_Email_Check' => 'S\'il vous plaît entrer correcte email',
		'_dbcs_fbc_FieldError_Email_Mandatory' => 'Adresse e-mail est requise',
		'_dbcs_fbc_FieldError_Email_Min' => 'Votre e-mail est trop court',
		'_dbcs_fbc_FieldError_Email_Unique' => 'Compte avec cet email existe déjà.',
		'_dbcs_fbc_FieldError_NickName_Check' => 'Votre nom d\'utilisateur ne doit contenir que de l\'alphabet latin, des chiffres ou de soulignement (_) ou moins (-) des signes',
		'_dbcs_fbc_FieldError_NickName_Mandatory' => 'Vous devez entrer Nom d\'utilisateur',
		'_dbcs_fbc_FieldError_NickName_Max' => 'Votre nom d\'utilisateur ne doit pas être plus long que {0} caractères',
		'_dbcs_fbc_FieldError_NickName_Min' => 'Votre nom d\'utilisateur doit être au moins {0} caractères',
		'_dbcs_fbc_FieldError_NickName_Unique' => 'Ce nom d\'utilisateur est déjà utilisé par un autre membre. S\'il vous plaît sélectionner un autre nom d\'utilisateur.',
		'_dbcs_fbc_FieldError_Password_Mandatory' => 'Password is required',
		'_dbcs_fbc_FieldError_Password_Max' => 'Your password should be no longer than {0} characters',
		'_dbcs_fbc_FieldError_Password_Min' => 'Your password must be at least {0} characters long',
		'_dbcs_fbc_FieldError_Sex_Mandatory' => 'Please, specify your gender',
		'_dbcs_fbc_FieldError_zip_Mandatory' => 'Please, specify your ZIP code',
		'_dbcs_fbc_FieldError_zip_Max' => 'Please, enter {0} character(s) or less',
		'_dbcs_fbc_FieldError_zip_Min' => 'Please, enter {0} character(s) or more',
		'_dbcs_fbc_FieldDesc_Profiletype_Join' => 'Select a profile type.',
		'_dbcs_fbc_FieldDesc_NickName_Join' => 'Choose a Username which will be used for logging in to the site',
		'_dbcs_fbc_FieldDesc_Password_Join' => 'Choose a password.',
		'_dbcs_fbc_FieldDesc_Password_Join_Confirm' => 'Confirm Password',
		'_dbcs_fbc_FieldDesc_Email_Join' => 'Enter your Email. Your password will be sent to this email.',
		'_dbcs_fbc_FieldDesc_DateOfBirth_Join' => 'Please specify your birth date: Year-Month-Day',
		'_dbcs_fbc_FieldDesc_Sex_Join' => 'Please specify your gender',
		'_dbcs_fbc_FieldDesc_Country_Join' => 'Select the country where you live ',
		'_dbcs_fbc_FieldDesc_City_Join' => 'Specify the city where you live ',
		'_dbcs_fbc_FieldDesc_zip_Join' => 'Enter your Post code (Zip)',

    );
