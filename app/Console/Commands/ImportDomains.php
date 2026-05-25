<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Services\NameSiloService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportDomains extends Command
{
    protected $signature = 'domains:import {--domains=} {--user-id=}';
    protected $description = 'Import domains with DNS/WHOIS lookup';

    private array $domains = [
        'motorcareuganda.com',
        'grethepractice.com',
        'condiadosesministries.com',
        'goodshedfoundation.org',
        'ugscope.com',
        'uiaa.co.ug',
        'bwengulaartsacademy.org',
        'b4ainvests.com',
        'bryanzlogistics.com',
        'kaimsoftsolutions.com',
        'kassma.net',
        'mycosofttechnologies.com',
        'jajjamuwanga.com',
        'innospecchem.com',
        'twesehamwe.org',
        'goldivaminerals.com',
        'doctoraliug.com',
    ];

    public function handle(): int
    {
        $domainsOption = $this->option('domains');
        $domains = !empty($domainsOption)
            ? explode(',', $domainsOption)
            : $this->domains;

        $userId = $this->option('user-id')
            ? (int) $this->option('user-id')
            : Auth::id();

        if (!$userId) {
            $this->error('No user ID provided and no authenticated user found.');
            return Command::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($domains));
        $bar->start();

        $imported = 0;
        $failed = 0;

        foreach ($domains as $domainName) {
            try {
                $this->importDomain($domainName, $userId);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed: {$domainName} - {$e->getMessage()}");
                Log::error("Domain import failed: {$domainName}", ['error' => $e->getMessage()]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import complete: {$imported} imported, {$failed} failed");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function importDomain(string $domainName, int $userId): void
    {
        $domainName = strtolower(trim($domainName));

        $existing = Domain::where('domain_name', $domainName)->first();
        if ($existing) {
            $this->line(" [SKIP] {$domainName} already exists");
            return;
        }

        $domainInfo = $this->lookupDomain($domainName);

        Domain::create([
            'user_id' => $userId,
            'domain_name' => $domainName,
            'status' => $domainInfo['status'] ?? 'active',
            'registration_date' => $domainInfo['registration_date'] ?? Carbon::now(),
            'expiry_date' => $domainInfo['expiry_date'] ?? Carbon::now()->addYear(),
            'registrar' => $domainInfo['registrar'] ?? 'Unknown',
            'provider' => $domainInfo['provider'] ?? null,
            'nameservers' => $domainInfo['nameservers'] ?? [],
            'dns_records' => $domainInfo['dns_records'] ?? [],
            'auto_renew' => false,
            'privacy_protection' => false,
        ]);

        $registrar = $domainInfo['registrar'] ?? 'Unknown';
        $this->line(" [ADDED] {$domainName} ({$registrar})");
    }

    private function lookupDomain(string $domain): array
    {
        $info = [
            'nameservers' => [],
            'dns_records' => [],
            'registrar' => 'Unknown',
            'provider' => null,
            'status' => 'active',
            'registration_date' => null,
            'expiry_date' => null,
        ];

        if (function_exists('dns_get_record')) {
            $dnsRecords = @dns_get_record($domain, DNS_NS | DNS_A | DNS_AAAA | DNS_MX);
            if ($dnsRecords) {
                foreach ($dnsRecords as $record) {
                    if (($record['type'] ?? '') === 'NS') {
                        $info['nameservers'][] = $record['target'];
                    }
                }
            }
        }

        if (empty($info['nameservers']) && function_exists('gethostbynamel')) {
            $nsServers = $this->suggestNameServers($domain);
            $info['nameservers'] = $nsServers;
        }

        $info['provider'] = $this->detectRegistrarByNS($info['nameservers']);

        $whoisInfo = $this->queryWhois($domain);
        if ($whoisInfo) {
            $info['registrar'] = $this->extractRegistrar($whoisInfo);
            $info['expiry_date'] = $this->extractExpiryDate($whoisInfo);
            $info['registration_date'] = $this->extractRegistrationDate($whoisInfo);
        }

        $info['expiry_date'] = $info['expiry_date'] ?? Carbon::now()->addYear();
        $info['registration_date'] = $info['registration_date'] ?? Carbon::now();

        return $info;
    }

    private function queryWhois(string $domain): ?string
    {
        $tld = explode('.', $domain);
        $tld = strtolower(end($tld));

        $whoisServers = [
            'com' => 'whois.verisign-grs.com',
            'net' => 'whois.verisign-grs.com',
            'org' => 'whois.pir.org',
            'ug' => 'whois.co.ug',
            'co.ug' => 'whois.co.ug',
        ];

        $server = $whoisServers[$tld] ?? null;
        if (!$server) {
            return null;
        }

        try {
            $socket = @fsockopen($server, 43, $errno, $errstr, 20);
            if (!$socket) {
                return null;
            }

            fwrite($socket, $domain . "\r\n");
            stream_set_timeout($socket, 20);

            $response = '';
            while (!feof($socket)) {
                $response .= fgets($socket, 4096);
            }
            fclose($socket);

            return $response ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function suggestNameServers(string $domain): array
    {
        $tld = explode('.', $domain);
        $tld = end($tld);

        $nsPatterns = [
            'com' => ['ns1.verification-hold.net', 'ns2.verification-hold.net'],
            'org' => ['ns1.verification-hold.net', 'ns2.verification-hold.net'],
            'net' => ['ns1.verification-hold.net', 'ns2.verification-hold.net'],
            'ug' => ['ns1.co.ug.net', 'ns2.co.ug.net'],
            'co.ug' => ['ns1.co.ug.net', 'ns2.co.ug.net'],
        ];

        return $nsPatterns[$tld] ?? ['ns1.common-config.net', 'ns2.common-config.net'];
    }

    private function extractRegistrar(string $whois): string
    {
        $patterns = [
            'Registrar:' => 'Registrar:',
            'Registrar Name:' => 'Registrar Name:',
            'Sponsoring Registrar:' => 'Sponsoring Registrar:',
        ];

        foreach ($patterns as $pattern => $label) {
            if (stripos($whois, $pattern) !== false) {
                preg_match("/{$pattern}\s*(.+)/i", $whois, $matches);
                if (!empty($matches[1])) {
                    return trim($matches[1]);
                }
            }
        }

        return 'Unknown';
    }

    private function extractExpiryDate(string $whois): ?Carbon
    {
        $patterns = [
            'Registry Expiry Date:' => 'Registry Expiry Date:',
            'Expiry Date:' => 'Expiry Date:',
            'Expiration Date:' => 'Expiration Date:',
            'expires:' => 'expires:',
            'Domain Expiration Date:' => 'Domain Expiration Date:',
        ];

        foreach ($patterns as $pattern => $label) {
            if (stripos($whois, $pattern) !== false) {
                preg_match("/{$pattern}\s*(.+)/i", $whois, $matches);
                if (!empty($matches[1])) {
                    try {
                        $dateStr = trim($matches[1]);
                        return Carbon::parse($dateStr);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return null;
    }

    private function extractRegistrationDate(string $whois): ?Carbon
    {
        $patterns = [
            'Creation Date:' => 'Creation Date:',
            'Created:' => 'Created:',
            'Registration Date:' => 'Registration Date:',
            'Created On:' => 'Created On:',
        ];

        foreach ($patterns as $pattern => $label) {
            if (stripos($whois, $pattern) !== false) {
                preg_match("/{$pattern}\s*(.+)/i", $whois, $matches);
                if (!empty($matches[1])) {
                    try {
                        $dateStr = trim($matches[1]);
                        return Carbon::parse($dateStr);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return null;
    }

    private function detectRegistrarByNS(array $nameservers): string
    {
        if (empty($nameservers)) {
            return 'Unknown';
        }

        $nsString = implode(' ', $nameservers);
        $nsString = strtolower($nsString);

        $registrarPatterns = [
            'namesilo' => ['namesilo', 'ns1.namesilo', 'ns2.namesilo'],
            'godaddy' => ['godaddy', 'ns1.secureserver', 'ns2.secureserver'],
            'cloudflare' => ['cloudflare', 'ns1.cloudflare', 'ns2.cloudflare'],
            'aws' => ['aws', 'awsdns', 'route53'],
            'google' => ['google', 'ns1.google', 'ns2.google'],
            'hover' => ['hover', 'ns1.hover', 'ns2.hover'],
            'namecheap' => ['namecheap', 'ns1.namecheap', 'ns2.namecheap'],
            'enom' => ['enom', 'ns1.enom', 'ns2.enom'],
            'register' => ['register', 'ns1.register', 'ns2.register'],
            'domain' => ['domain.com', 'ns1.domain', 'ns2.domain'],
            'hostinger' => ['dns-parking.com', 'ns1.dns-parking.com', 'ns2.dns-parking.com', 'byte.dns-parking.com', 'pixel.dns-parking.com', 'athena.dns-parking.com', 'apollo.dns-parking.com'],
            'ultahost' => ['ultahost.com', 'ns1.ultahost.com', 'ns2.ultahost.com'],
            'africa' => ['africadomainregistry.com', 'ns541.africadomainregistry.com', 'ns542.africadomainregistry.com'],
        ];

        foreach ($registrarPatterns as $registrar => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($nsString, strtolower($pattern)) !== false) {
                    return ucfirst($registrar);
                }
            }
        }

        return 'Unknown';
    }
}
