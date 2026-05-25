<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class NameSiloService
{
    private string $apiKey;
    private string $baseUrl;
    private string $version;
    private string $format;

    public function __construct()
    {
        $this->apiKey = config('services.namesilo.api_key', env('NAMESILO_API_KEY'));
        $this->baseUrl = config('services.namesilo.base_url', 'https://www.namesilo.com/api');
        $this->version = config('services.namesilo.version', '1');
        $this->format = config('services.namesilo.format', 'json');
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(string $domain): array
    {
        try {
            $response = $this->makeRequest('checkRegisterAvailability', [
                'domains' => $domain
            ]);

            if ($response['reply']['code'] === 300) {
                $availableData = $response['reply']['available']['domain'] ?? null;
                
                return [
                    'available' => $availableData !== null,
                    'domain' => $domain,
                    'price' => $availableData['price'] ?? null,
                    'renew_price' => $availableData['renew'] ?? null,
                    'premium' => $availableData['premium'] ?? 0,
                    'duration' => $availableData['duration'] ?? 1,
                    'message' => $response['reply']['detail'] ?? 'Domain check completed'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to check domain availability');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Check Availability: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Register a domain
     */
    public function registerDomain(array $domainData): array
    {
        try {
            $params = [
                'domain' => $domainData['domain_name'],
                'years' => $domainData['years'] ?? 1,
                'private' => $domainData['privacy_protection'] ? 'yes' : 'no',
                'auto_renew' => $domainData['auto_renew'] ? 'yes' : 'no',
            ];

            // Add contact information if provided
            if (isset($domainData['contact_email'])) {
                $params['contact_email'] = $domainData['contact_email'];
            }

            $response = $this->makeRequest('registerDomain', $params);

            if ($response['reply']['code'] === 300) {
                return [
                    'success' => true,
                    'domain' => $domainData['domain_name'],
                    'order_id' => $response['reply']['order_id'] ?? null,
                    'message' => $response['reply']['detail'] ?? 'Domain registered successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to register domain');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Register Domain: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Renew a domain
     */
    public function renewDomain(string $domain, int $years = 1): array
    {
        try {
            $response = $this->makeRequest('renewDomain', [
                'domain' => $domain,
                'years' => $years
            ]);

            if ($response['reply']['code'] === 300) {
                return [
                    'success' => true,
                    'domain' => $domain,
                    'order_id' => $response['reply']['order_id'] ?? null,
                    'new_expiry' => $response['reply']['new_expiry'] ?? null,
                    'message' => $response['reply']['detail'] ?? 'Domain renewed successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to renew domain');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Renew Domain: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get domain information
     */
    public function getDomainInfo(string $domain): array
    {
        try {
            $response = $this->makeRequest('getDomainInfo', [
                'domain' => $domain
            ]);

            if ($response['reply']['code'] === 300) {
                // Handle nameservers properly - NameSilo returns array of objects with 'nameserver' property
                $nameservers = [];
                if (isset($response['reply']['nameservers']) && is_array($response['reply']['nameservers'])) {
                    foreach ($response['reply']['nameservers'] as $ns) {
                        if (is_array($ns) && isset($ns['nameserver'])) {
                            $nameservers[] = $ns['nameserver'];
                        } elseif (is_string($ns)) {
                            $nameservers[] = $ns;
                        }
                    }
                }

                return [
                    'success' => true,
                    'domain' => $domain,
                    'status' => $response['reply']['status'] ?? 'unknown',
                    'expires' => $response['reply']['expires'] ?? null,
                    'created' => $response['reply']['created'] ?? null,
                    'updated' => $response['reply']['updated'] ?? null,
                    'nameservers' => $nameservers,
                    'locked' => $response['reply']['locked'] ?? 'no',
                    'auto_renew' => $response['reply']['auto_renew'] ?? 'no',
                    'private' => $response['reply']['private'] ?? 'no',
                    'message' => $response['reply']['detail'] ?? 'Domain information retrieved'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to get domain information');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Get Domain Info: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List all domains
     */
    public function listDomains(): array
    {
        try {
            $response = $this->makeRequest('listDomains');

            if ($response['reply']['code'] === 300) {
                $domains = [];
                if (isset($response['reply']['domains'])) {
                    foreach ($response['reply']['domains'] as $domainData) {
                        $domains[] = [
                            'domain' => $domainData['domain'],
                            'created' => $domainData['created'],
                            'expires' => $domainData['expires'],
                            'status' => 'active', // Default status since API doesn't provide it
                        ];
                    }
                }
                
                return [
                    'success' => true,
                    'domains' => $domains,
                    'message' => $response['reply']['detail'] ?? 'Domains retrieved successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to list domains');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - List Domains: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get domain nameservers
     */
    public function getDomainNameservers(string $domain): array
    {
        try {
            $response = $this->makeRequest('getDomainInfo', [
                'domain' => $domain
            ]);

            if ($response['reply']['code'] === 300) {
                // Handle nameservers properly - NameSilo returns array of objects with 'nameserver' property
                $nameservers = [];
                if (isset($response['reply']['nameservers']) && is_array($response['reply']['nameservers'])) {
                    foreach ($response['reply']['nameservers'] as $ns) {
                        if (is_array($ns) && isset($ns['nameserver'])) {
                            $nameservers[] = $ns['nameserver'];
                        } elseif (is_string($ns)) {
                            $nameservers[] = $ns;
                        }
                    }
                }

                return [
                    'success' => true,
                    'domain' => $domain,
                    'nameservers' => $nameservers,
                    'message' => 'Nameservers retrieved successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to get domain nameservers');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Get Domain Nameservers: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update domain nameservers
     */
    public function updateNameservers(string $domain, array $nameservers): array
    {
        try {
            $params = ['domain' => $domain];
            
            // Add nameservers (up to 4)
            for ($i = 0; $i < min(4, count($nameservers)); $i++) {
                $params["ns{$i}"] = $nameservers[$i];
            }

            $response = $this->makeRequest('changeNameServers', $params);

            if ($response['reply']['code'] === 300) {
                return [
                    'success' => true,
                    'domain' => $domain,
                    'nameservers' => $nameservers,
                    'message' => $response['reply']['detail'] ?? 'Nameservers updated successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to update nameservers');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Update Nameservers: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get domain pricing (using checkRegisterAvailability for pricing info)
     */
    public function getDomainPricing(string $domain): array
    {
        try {
            // Use checkRegisterAvailability to get pricing information
            $response = $this->makeRequest('checkRegisterAvailability', [
                'domains' => $domain
            ]);

            if ($response['reply']['code'] === 300) {
                $availableData = $response['reply']['available']['domain'] ?? null;
                
                return [
                    'success' => true,
                    'domain' => $domain,
                    'registration' => $availableData['price'] ?? null,
                    'renewal' => $availableData['renew'] ?? null,
                    'premium' => $availableData['premium'] ?? 0,
                    'message' => $response['reply']['detail'] ?? 'Pricing retrieved successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to get domain pricing');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Get Domain Pricing: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Transfer domain to NameSilo
     */
    public function transferDomain(array $transferData): array
    {
        try {
            $params = [
                'domain' => $transferData['domain_name'],
                'auth' => $transferData['auth_code'],
                'years' => $transferData['years'] ?? 1,
                'private' => $transferData['privacy_protection'] ? 'yes' : 'no',
                'auto_renew' => $transferData['auto_renew'] ? 'yes' : 'no',
            ];

            $response = $this->makeRequest('transferDomain', $params);

            if ($response['reply']['code'] === 300) {
                return [
                    'success' => true,
                    'domain' => $transferData['domain_name'],
                    'order_id' => $response['reply']['order_id'] ?? null,
                    'message' => $response['reply']['detail'] ?? 'Domain transfer initiated successfully'
                ];
            }

            throw new Exception($response['reply']['detail'] ?? 'Failed to transfer domain');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Transfer Domain: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Make API request to NameSilo
     */
    private function makeRequest(string $operation, array $params = []): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('NameSilo API key is not configured');
        }

        $url = "{$this->baseUrl}/{$operation}";
        
        $queryParams = array_merge([
            'version' => $this->version,
            'type' => $this->format,
            'key' => $this->apiKey,
        ], $params);

        $response = Http::timeout(30)->get($url, $queryParams);

        if (!$response->successful()) {
            throw new Exception("HTTP request failed with status: {$response->status()}");
        }

        $data = $response->json();

        if (!$data || !isset($data['reply'])) {
            throw new Exception('Invalid response format from NameSilo API');
        }

        return $data;
    }

    /**
     * Validate domain name format
     */
    public function validateDomainName(string $domain): bool
    {
        // Basic domain validation
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    /**
     * Sync domains from NameSilo account to local database
     */
    public function syncDomainsFromAccount(int $userId): array
    {
        try {
            $response = $this->listDomains();
            
            if ($response['success']) {
                $syncedDomains = [];
                $errors = [];
                
                foreach ($response['domains'] as $domainData) {
                    try {
                        // Check if domain already exists in local database
                        $existingDomain = \App\Models\Domain::where('user_id', $userId)
                            ->where('domain_name', $domainData['domain'])
                            ->first();
                        
                        if ($existingDomain) {
                            // Update existing domain
                            $existingDomain->update([
                                'expiry_date' => \Carbon\Carbon::parse($domainData['expires']),
                                'registration_date' => \Carbon\Carbon::parse($domainData['created']),
                                'status' => $domainData['status'],
                            ]);
                            $syncedDomains[] = $domainData['domain'] . ' (updated)';
                        } else {
                            // Create new domain record
                            \App\Models\Domain::create([
                                'user_id' => $userId,
                                'domain_name' => $domainData['domain'],
                                'status' => $domainData['status'],
                                'registration_date' => \Carbon\Carbon::parse($domainData['created']),
                                'expiry_date' => \Carbon\Carbon::parse($domainData['expires']),
                                'registrar' => 'NameSilo',
                            ]);
                            $syncedDomains[] = $domainData['domain'] . ' (added)';
                        }
                    } catch (\Exception $e) {
                        $errors[] = $domainData['domain'] . ': ' . $e->getMessage();
                    }
                }
                
                return [
                    'success' => true,
                    'synced_domains' => $syncedDomains,
                    'errors' => $errors,
                    'total_processed' => count($response['domains']),
                    'message' => 'Domain sync completed'
                ];
            }
            
            throw new Exception('Failed to retrieve domains from NameSilo');
        } catch (Exception $e) {
            Log::error('NameSilo API Error - Sync Domains: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get supported TLDs (this would typically come from NameSilo API)
     */
    public function getSupportedTlds(): array
    {
        return [
            '.com', '.net', '.org', '.info', '.biz', '.co', '.me', '.us', '.ca', '.uk',
            '.de', '.fr', '.it', '.es', '.nl', '.be', '.ch', '.at', '.se', '.no',
            '.dk', '.fi', '.pl', '.cz', '.hu', '.ro', '.bg', '.hr', '.si', '.sk',
            '.lt', '.lv', '.ee', '.ie', '.pt', '.gr', '.cy', '.mt', '.lu', '.is'
        ];
    }
}
