# SysAdmin Toolkit for All

A lightweight, stateless web application for sysadmins — networking tools, reference tables and quick lookups, all in one place. No login, no tracking, no JavaScript frameworks to install.

## Tools (21)

### Subnet & IP
| Tool | Route | Description |
|------|-------|-------------|
| Subnet Calculator (IPv4) | `/tools/subnet-calculator` | Network, broadcast, host range and netmask from IP + CIDR |
| IPv6 Subnet Calculator | `/tools/ipv6-calculator` | First/last address, compressed/expanded form, total addresses |
| CIDR Cheat Sheet | `/tools/cidr-cheatsheet` | All IPv4 prefixes /0–/32 with netmask, wildcard and host count |
| VLAN Calculator | `/tools/vlan-calculator` | Split a base network into equal subnets with sequential VLAN IDs |
| Bandwidth Calculator | `/tools/bandwidth-calculator` | Transfer time and throughput with configurable overhead |

### Diagnostics & Lookup
| Tool | Route | Description |
|------|-------|-------------|
| Ping / Traceroute | `/tools/ping-traceroute` | Reachability check and hop-by-hop path trace |
| DNS Lookup | `/tools/dns-lookup` | Query A, AAAA, MX, NS, TXT, CNAME, SOA and PTR records |
| Whois Lookup | `/tools/whois` | Raw Whois output for domains and IP addresses |
| IP Geolocation | `/tools/ip-geolocation` | Country, city, ISP, coordinates and timezone for any IP |
| SSL/TLS Checker | `/tools/ssl-checker` | Certificate validity, expiry, issuer and SHA-256 fingerprint |
| MAC Address Lookup | `/tools/mac-lookup` | Identify manufacturer from MAC address (39 000+ OUI entries, IEEE MA-L) |

### References
| Tool | Route | Description |
|------|-------|-------------|
| Port Reference | `/tools/port-reference` | Search 500+ well-known TCP/UDP ports by number or service name |
| OSI Model Reference | `/tools/osi-model` | All 7 layers with PDU, protocols, devices and descriptions |
| HTTP Status Codes | `/tools/http-status-codes` | All HTTP status codes grouped by class with descriptions |
| RFC Browser | `/tools/rfc-browser` | 75+ key RFCs indexed by category and status, link to RFC Editor |

### Cabling
| Tool | Route | Description |
|------|-------|-------------|
| Network Cable Schemas | `/tools/cable-schemas` | T568A/B wiring, straight-through, crossover and rollover diagrams |
| Cable Colors T568A/B | `/tools/cable-colors` | Visual RJ45 colour-coding reference with differing pins highlighted |

### Tools
| Tool | Route | Description |
|------|-------|-------------|
| Linux Cheatsheet | `/tools/linux-cheatsheet` | 97 commands across 8 categories with examples, client-side search |
| Regex Tester | `/tools/regex-tester` | Test regular expressions with real-time match highlighting |
| Base Converter | `/tools/base-converter` | Convert integers between binary, octal, decimal and hexadecimal |
| Formatter | `/tools/formatter` | Prettify and syntax-highlight JSON, XML and HTML |

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade + Alpine.js (CDN) + htmx (CDN) |
| Styles | Tailwind CSS (CDN Play) |
| Database | SQLite — read-only reference data (OUI vendor table) |
| Web server | Nginx (Alpine) |
| Containers | Docker + Docker Compose — exactly 2 containers |
| Testing | PHPUnit 12 (feature tests) |
| i18n | Laravel lang files — Italian (default) and English |

No Node.js, no npm, no build step. The entire frontend weighs under 30 KB of CDN scripts.

## Requirements

- Docker Engine 20+
- Docker Compose v2+
- Internet access (for IP Geolocation, DNS Lookup, the OUI seeder, and the public IP banner)

## Quick Start

```bash
# 1. Clone
git clone https://github.com/lejubila/SAT4all.git
cd SAT4all

# 2. Environment
cp .env.example .env

# 3. Fix storage permissions (required on Linux)
chmod -R 775 storage bootstrap/cache

# 4. Build and start containers
docker compose up -d --build

# 5. Install PHP dependencies
docker compose exec app composer install --no-dev --optimize-autoloader

# 6. Generate app key
docker compose exec app php artisan key:generate

# 7. Run database migrations
docker compose exec app php artisan migrate

# 8. Seed the OUI vendor database (~39 000 IEEE entries, requires internet)
docker compose exec app php artisan db:seed --class=OuiVendorSeeder
```

Open **http://localhost:8080** in your browser (or the port set in `APP_PORT`).

> The OUI seeder downloads the IEEE MA-L CSV (~3 MB) once and takes about 30 seconds.  
> The MAC Address Lookup tool falls back to an embedded list of ~400 common vendors if the seeder has not been run yet.

## Development

### Useful commands

```bash
# Start / stop containers
docker compose up -d
docker compose down

# Run all tests (215 tests, ~4 s)
docker compose exec app php artisan test

# Run a single test class
docker compose exec app php artisan test --filter=SubnetCalculator

# Open a shell inside the PHP container
docker compose exec app sh

# Tail application logs
docker compose logs -f app

# Run migrations
docker compose exec app php artisan migrate

# Create a new controller
docker compose exec app php artisan make:controller Tools/MyToolController

# Create a new Form Request
docker compose exec app php artisan make:request Tools/MyToolRequest
```

### Adding a new tool

1. Create the logic class: `app/Tools/{Name}/{Name}.php` — pure PHP, no HTTP/framework dependencies
2. Create the Form Request: `app/Http/Requests/Tools/{Name}Request.php`
3. Create the controller: `app/Http/Controllers/Tools/{Name}Controller.php`
4. Add routes in `routes/web.php` with prefix `/tools` and name `tools.{name}.*`
5. Add translations in **both** `lang/it/tools.php` and `lang/en/tools.php`
6. Create the main view: `resources/views/tools/{name}.blade.php`
7. Create the htmx partial: `resources/views/tools/partials/{name}-result.blade.php`
8. Add the menu entry in `resources/views/layouts/app.blade.php`
9. Write the feature test: `tests/Feature/Tools/{Name}Test.php`
10. Update the tool table in `CLAUDE.md` and in `resources/views/home.blade.php`

### Key conventions

- **htmx** handles form submissions — controllers always return Blade partials, never JSON.
- **Validation errors** are returned with HTTP 200 so htmx replaces the target element correctly (4xx would prevent the swap).
- **Alpine.js** manages local UI state only: loading spinners, tabs, accordions. No business logic.
- **No logic in the frontend** — all calculation and lookup happens server-side in PHP.
- **i18n** — both `it` and `en` translation keys must always be kept in sync.

### Project structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Tools/   # One controller per tool
│   │   └── Requests/Tools/      # Form Requests for input validation
│   └── Tools/                   # Pure PHP logic classes
│       ├── SubnetCalculator/
│       ├── MacLookup/
│       └── ...
├── database/
│   ├── migrations/              # oui_vendors table
│   └── seeders/
│       └── OuiVendorSeeder.php  # Downloads IEEE MA-L CSV
├── docker/
│   └── php/
│       ├── Dockerfile           # php:8.3-fpm-alpine + cap_net_raw for traceroute
│       └── nginx/default.conf
├── lang/
│   ├── it/                      # Italian translations (default)
│   └── en/                      # English translations
├── resources/views/
│   ├── layouts/app.blade.php    # Main layout: navbar, public IP banner, language switcher
│   ├── home.blade.php
│   └── tools/
│       ├── *.blade.php          # One main view per tool
│       └── partials/            # htmx partial views (form results)
└── tests/Feature/Tools/         # One PHPUnit test class per tool
```

## Testing

```bash
# Full suite
docker compose exec app php artisan test

# Single tool
docker compose exec app php artisan test --filter=MacLookup
```

Tests use SQLite `:memory:` (configured in `phpunit.xml`) — no external services required. Test classes that touch the database use `RefreshDatabase` and seed the minimal rows they need in `setUp()`.

## Security notes

- **Ping / Traceroute** — uses `proc_open()` with an array command (bypasses the shell entirely, no injection possible). `cap_net_raw` is granted via `setcap` to a dedicated copy of the `traceroute` binary, not to the PHP process.
- **Whois** — same `proc_open()` array approach; target is validated against a strict hostname/IP regex before the command is executed.
- **IP Geolocation** — uses the public `ip-api.com` API. No API key required; results are cached for 1 hour per IP to avoid hammering the endpoint.
- **DNS Lookup** — rate-limited to 60 requests per minute via Laravel's built-in throttle middleware.
- **MAC Address Lookup** — fully local at lookup time; the OUI seeder runs once and stores data in SQLite.

## Internationalization

The default language is **Italian**. Click the `IT / EN` toggle in the navbar to switch. The selected language is persisted in the Laravel session (server-side, no cookies required).

To add a new language:
1. Create `lang/{locale}/ui.php` and `lang/{locale}/tools.php` following the existing files.
2. Add the locale to the whitelist in `app/Http/Middleware/SetLocale.php`.
3. Add the button to the language switcher in `resources/views/layouts/app.blade.php`.

## License

MIT — see [LICENSE](LICENSE).
