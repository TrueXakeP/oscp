<?php

namespace OpenServer\DTO;

use OpenServer\Services\Modules;

/**
 * @property-read string host
 * @property-read string aliases
 * @property-read string engine
 * @property-read string root_directory
 * @property-read bool enabled
 * @property-read string cgi_directory
 * @property-read string ip
 * @property-read string log_format
 * @property-read bool self_config
 * @property-read bool ssl
 * @property-read string ssl_cert_file
 * @property-read string ssl_key_file
 * @property-read string admin_path
 * @property-read string project_command
 * @property-read string project_modules
 * @property-read string project_url
 * @property-read bool project_use_win_env
 */
class Domain
{
    protected array $data;
    protected ?Module $module;

    public function __construct(array $data)
    {
        $this->data = [
            ...$data,
            'host'                => $data['host'],
            'aliases'             => $data['aliases'] ?? '',
            'engine'              => $data['engine'] ?? 'PHP-8.1',
            'root_directory'      => $this->path(str_replace('{host}', $data['host'], $data['root_directory'])),
            'enabled'             => (bool)($data['enabled'] ?? true),
            'cgi_directory'       => $this->path($data['cgi_directory'] ?? ''),
            'ip'                  => $data['ip'] ?? 'auto',
            'log_format'          => $data['log_format'] ?? 'combined',
            'self_config'         => (bool)($data['self_config'] ?? false),
            'ssl'                 => (bool)($data['ssl'] ?? false),
            'ssl_cert_file'       => $this->path($data['ssl_cert_file'] ?? '{root_dir}/user/ssl/default/cert.crt'),
            'ssl_key_file'        => $this->path($data['ssl_key_file'] ?? '{root_dir}/user/ssl/default/cert.key'),
            'project_command'     => str_replace('&#38;', '&', $data['project_command'] ?? ''),
            'project_modules'     => $data['project_modules'] ?? '',
            'project_url'         => $data['project_url'] ?? 'http'.($data['ssl'] ?? false ? 's' : '').'://{host}',
            'project_use_win_env' => (bool)($data['project_use_win_env'] ?? false),
        ];
        $this->module = Modules::make()->get($this->engine);
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function isValidRoot(): bool
    {
        return file_exists(str_replace('{host}', $this->host, $this->root_directory));
    }

    public function isAvailable(): bool
    {
        return (bool)$this->module?->enabled;
    }

    public function realIp(): string
    {
        return (string)$this->module?->ip();
    }

    public function realPort(): string
    {
        return (string)$this->module?->port();
    }

    public function siteUrl(): string
    {
        return !empty($this->project_url) ? $this->project_url : 'http'.($this->ssl ? 's' : '').'://'.$this->host;
    }

    public function adminUrl(): string
    {
        if(str_contains($this->admin_path, '://')) {
            return $this->admin_path;
        }
        return $this->siteUrl().'/'.ltrim($this->admin_path, '/');
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function path(string $path): string
    {
        return toUnixPath(absolutePath($path));
    }
}
