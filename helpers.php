<?php
// helpers.php

/**
 * Formata CNPJ
 */


/**
 * Formata data no padrão brasileiro
 */

/**
 * Formata data e hora no padrão brasileiro
 */
function formatarDataHora($dataHora) {
    if (empty($dataHora) || $dataHora === '0000-00-00 00:00:00') return '-';
    return date('d/m/Y H:i:s', strtotime($dataHora));
}

/**
 * Formata telefone
 */


/**
 * Formata CEP
 */