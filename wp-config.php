<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'boutiqueline_db' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':pu[@PiUfw&-gf[!@;)?@,iT]&,e11.exck]0v*3R]I;4LTSp oR#t7pg1zjq%`b' );
define( 'SECURE_AUTH_KEY',  'IxMf6i8^YbamMSbFQay1)(5U7pf&h=/9M`Y?[KU6|zKqk4OcOjXz@2G?*),kRt;2' );
define( 'LOGGED_IN_KEY',    '{d(xc@:Ut`2l+N3JQD3zQ3!1N5z>%w] %);V0ZE>+N|90a:68&hR&/7`nCZ>3g33' );
define( 'NONCE_KEY',        'rSHoYdeVbm.fwzuH{+]!bmuz*5=4I3hyT8@|g4bgN 5TQ37-SLkkVzWt3+xy3rZ$' );
define( 'AUTH_SALT',        'XD5lCh}gqH %>q4:Ob,TlIAE=Pj KW*$mjE=x&bML&*A:r9j]k7g8n@!l9_ 9HBW' );
define( 'SECURE_AUTH_SALT', '#`}viz1nmH<UWVSqfK/wwZqZmgcp} JT}}nvI~WYcJ[]P,e1tv=f#Inf!6!Pq78Z' );
define( 'LOGGED_IN_SALT',   'kAioQnMq8o(Q^=i-rO>bFZW,;Pvl(R9Bie~xy+N~nMjIMj2HW5cSKoxuQ]=}Mlb&' );
define( 'NONCE_SALT',       'OQP!tjTR(KX#oJL=DDoaEP4|1l@`{R2N<3lAB.4i.$wV!ksUpk[|gNh/Jwlj?g:f' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
