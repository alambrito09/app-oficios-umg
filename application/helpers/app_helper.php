<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('esc')) {
    function esc($value) {
        return html_escape($value);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        $CI =& get_instance();
        return form_hidden($CI->security->get_csrf_token_name(), $CI->security->get_csrf_hash());
    }
}

if (!function_exists('status_badge_class')) {
    function status_badge_class($status) {
        $classes = array(
            'solicitado' => 'primary',
            'en_evaluacion' => 'warning',
            'pendiente_aprobacion' => 'info',
            'confirmado' => 'success',
            'en_ejecucion' => 'primary',
            'finalizado' => 'success',
            'rechazado' => 'danger',
            'vencido' => 'secondary',
            'cancelado' => 'danger',
            'programado' => 'primary',
            'completado' => 'success',
            'cerrado' => 'secondary',
            'abierto' => 'danger',
            'en_proceso' => 'warning',
            'resuelto' => 'success'
        );

        return isset($classes[$status]) ? $classes[$status] : 'secondary';
    }
}
