<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['email_must_be_array'] = 'La validación del correo debe recibir un arreglo.';
$lang['email_invalid_address'] = 'Dirección de correo no válida: %s';
$lang['email_attachment_missing'] = 'No se encontró el archivo adjunto: %s';
$lang['email_attachment_unreadable'] = 'No se pudo abrir el archivo adjunto: %s';
$lang['email_no_from'] = 'No se puede enviar el correo sin un remitente.';
$lang['email_no_recipients'] = 'Debe indicar al menos un destinatario en Para, CC o CCO.';
$lang['email_send_failure_phpmail'] = 'No se pudo enviar el correo mediante PHP mail().';
$lang['email_send_failure_sendmail'] = 'No se pudo enviar el correo mediante PHP Sendmail.';
$lang['email_send_failure_smtp'] = 'No se pudo enviar el correo mediante SMTP.';
$lang['email_sent'] = 'El mensaje se envió correctamente mediante el protocolo: %s';
$lang['email_no_socket'] = 'No se pudo abrir una conexión con Sendmail. Revise la configuración.';
$lang['email_no_hostname'] = 'No se indicó el servidor SMTP.';
$lang['email_smtp_error'] = 'Se produjo el siguiente error SMTP: %s';
$lang['email_no_smtp_unpw'] = 'Debe configurar el usuario y la contraseña SMTP.';
$lang['email_failed_smtp_login'] = 'No se pudo enviar el comando de autenticación SMTP: %s';
$lang['email_smtp_auth_un'] = 'No se pudo autenticar el usuario SMTP: %s';
$lang['email_smtp_auth_pw'] = 'No se pudo autenticar la contraseña SMTP: %s';
$lang['email_smtp_data_failure'] = 'No se pudieron enviar los datos del correo: %s';
$lang['email_exit_status'] = 'Código de salida: %s';
