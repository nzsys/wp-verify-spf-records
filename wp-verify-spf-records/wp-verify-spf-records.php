<?php
/*
Plugin Name: Verify SPF records
Plugin URI:
Description: This is a plugin for validating SPF records in WordPress. Verify SPF records from FQDN or email address.
Version: 1.0.0
Author: nzsys
License: MIT
*/

use Nzsys\EnvelopFixer\App\SPFRecord;
use Nzsys\EnvelopFixer\App\IpConverter;
use Nzsys\EnvelopFixer\Spec\Email;
use Nzsys\EnvelopFixer\Spec\FQDN;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

add_action('phpmailer_init', static function($phpmailer){
  $phpmailer->SMTPKeepAlive = true;
  $phpmailer->Sender = get_option('envelope_from_email') ?? $phpmailer->From;
});


add_action('admin_menu', static function () {
    add_options_page($_ENV['PLUGIN_TITLE'], $_ENV['PLUGIN_TITLE'], 'manage_options', 'validate_hostname', 'validateHostname');
});

function validateHostname(): void {
    canViewPlugin();
    $errorMessage = [];
    if (!csrf()) {
        $errorMessage[] = 'Unable to verify your request. Please refresh the page and try again.';
    }
    $viewResult = false;
    if (csrf() && filter_input(INPUT_POST, 'type')) {
        try {
            if (filter_input(INPUT_POST, 'type') === 'Email') {
                $hostnameInterface = new Email(filter_input(INPUT_POST, 'mail_host_address'));
            }
            if (filter_input(INPUT_POST, 'type') === 'FQDN') {
                $hostnameInterface = new FQDN(filter_input(INPUT_POST, 'mail_host_address'));
            }
            if (empty($hostnameInterface)) {
                $errorMessage[] = 'Invalid FQDN or Email.';
            }
            $spfRecord = new SpfRecord($hostnameInterface);
            $converter = new IpConverter($hostnameInterface);
            $hasIp = $spfRecord->isIpInclude($converter->ipAddress());
            $viewResult = true;
        } catch (Exception $e) {
            $errorMessage[] = $e->getMessage();
        }
    }
    require_once __DIR__ . '/' . $_ENV['PLUGIN_PAGE_VALIDATE'];
}

function canViewPlugin(): void {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
}

function csrf(): bool {
    $nonce = filter_input(INPUT_POST, $_ENV['PLUGIN_NONCE'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (null === $nonce) {
        return true;
    }
    return $nonce && wp_verify_nonce($nonce, $_ENV['PLUGIN_ACTION']);
}
