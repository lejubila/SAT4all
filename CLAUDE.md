# SysAdmin Toolkit for All — CLAUDE.md

> Istruzioni persistenti per Claude Code. Leggere integralmente prima di ogni sessione.

---

## 📌 Descrizione del Progetto

**Nome:** SysAdmin Toolkit for All
**Scopo:** Applicazione web leggera per sistemisti con tools di networking e riferimenti tecnici.
**Lingue:** Italiano (default) e Inglese — tramite sistema i18n Laravel.
**URL locale:** http://localhost:8080

---

## 🧱 Stack Tecnologico

| Layer      | Tecnologia                                      |
|------------|-------------------------------------------------|
| Backend    | Laravel 13, PHP 8.3                             |
| Frontend   | Blade templates + Alpine.js (CDN) + htmx (CDN) |
| Stile      | Tailwind CSS (CDN Play)                         |
| Database   | **SQLite** — solo dati di riferimento read-only (OUI vendor) |
| Web server | Nginx (Alpine)                                  |
| Container  | Docker + Docker Compose (2 container)           |
| Testing    | PHPUnit (feature)                               |
| i18n       | Laravel Lang (file PHP)                         |

---

## 🐳 Docker — Regole

- Il progetto usa **esattamente 2 container** — non aggiungerne altri.
- **Non modificare** `docker-compose.yml` o i Dockerfile senza chiedere esplicitamente.
- Porte riservate:
  - `8080` → Nginx (app)
- I container si chiamano: `sta_app`, `sta_nginx`
- Immagini base **Alpine** obbligatorie per minimizzare le dimensioni:
  - `php:8.3-fpm-alpine`
  - `nginx:alpine`
- Variabili d'ambiente in `.env` (mai hardcoded nel codice)
- Nessun Node.js, nessun npm, nessun build step — CSS e JS via CDN
- Per eseguire comandi PHP usare sempre: `docker compose exec app php artisan ...`

```yaml
# docker-compose.yml (struttura attesa)
services:
  app:
    build: ./docker/php
    container_name: sta_app
    volumes:
      - .:/var/www/html
  nginx:
    image: nginx:alpine
    container_name: sta_nginx
    ports:
      - "8080:80"
    depends_on:
      - app
```

---

## 📁 Struttura Cartelle

```
sysadmin-toolkit-for-all/
├── CLAUDE.md                          ← questo file
├── docker-compose.yml
├── docker/
│   └── php/
│       └── Dockerfile                 ← php:8.3-fpm-alpine + estensioni minime
│       └── nginx/default.conf
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── LanguageController.php ← cambio lingua IT/EN
│   │       └── Tools/                 ← un controller per tool
│   └── Tools/                         ← logica PHP pura (no HTTP, no framework)
│       ├── SubnetCalculator/
│       ├── CableSchemas/
│       ├── PortReference/
│       └── ...
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          ← layout principale con navbar e switcher lingua
│       ├── home.blade.php
│       └── tools/                     ← una view Blade per tool
│           ├── subnet-calculator.blade.php
│           ├── cable-schemas.blade.php
│           └── ...
│
├── lang/
│   ├── it/
│   │   ├── tools.php                  ← traduzioni tool IT
│   │   └── ui.php                     ← traduzioni UI IT
│   └── en/
│       ├── tools.php                  ← traduzioni tool EN
│       └── ui.php                     ← traduzioni UI EN
│
├── routes/
│   └── web.php                        ← rotte /tools/* + POST /language/{locale}
│
└── tests/
    └── Feature/
        └── Tools/                     ← test per ogni tool
```

---

## 🔧 Tools Pianificati

### Fase 1 — Core Networking
| Tool | Rotta | Status |
|------|-------|--------|
| Subnet Calculator (IPv4) | `/tools/subnet-calculator` | ✅ fatto |
| IPv6 Subnet Calculator | `/tools/ipv6-calculator` | ✅ fatto |
| Port Reference (TCP/UDP) | `/tools/port-reference` | ✅ fatto |
| Schemi Cavi di Rete | `/tools/cable-schemas` | ✅ fatto |
| CIDR Cheat Sheet | `/tools/cidr-cheatsheet` | ✅ fatto |

### Fase 2 — Tools Avanzati
| Tool | Rotta | Status |
|------|-------|--------|
| VLAN Calculator | `/tools/vlan-calculator` | ✅ fatto |
| DNS Lookup | `/tools/dns-lookup` | ✅ fatto |
| IP Geolocation | `/tools/ip-geolocation` | ✅ fatto |
| OSI Model Reference | `/tools/osi-model` | ✅ fatto |
| Ping / Traceroute | `/tools/ping-traceroute` | ✅ fatto |

### Fase 3 — Riferimenti
| Tool | Rotta | Status |
|------|-------|--------|
| Linux Commands Cheatsheet | `/tools/linux-cheatsheet` | ✅ fatto |
| Cable Color Code T568A/B | `/tools/cable-colors` | ✅ fatto |
| RFC Browser | `/tools/rfc-browser` | ✅ fatto |

### Fase 4 — Utilità e Lookup
| Tool | Rotta | Status |
|------|-------|--------|
| Whois Lookup | `/tools/whois` | ✅ fatto |
| MAC Address Lookup | `/tools/mac-lookup` | ✅ fatto |
| HTTP Status Codes | `/tools/http-status-codes` | ✅ fatto |
| Regex Tester | `/tools/regex-tester` | ✅ fatto |
| SSL/TLS Checker | `/tools/ssl-checker` | ✅ fatto |
| Base Converter | `/tools/base-converter` | ✅ fatto |
| Bandwidth Calculator | `/tools/bandwidth-calculator` | ✅ fatto |
| Formatter | `/tools/formatter` | ✅ fatto |
| Markdown Viewer | `/tools/markdown-viewer` | ✅ fatto |

### Fase 5 — Email
| Tool | Rotta | Status |
|------|-------|--------|
| Email Header Analyzer | `/tools/email-header-analyzer` | ✅ fatto |
| Email Deliverability Checker | `/tools/email-deliverability` | ✅ fatto |
| Blacklist / RBL Checker | `/tools/blacklist-checker` | ✅ fatto |
| MX Checker avanzato | `/tools/mx-checker` | ✅ fatto |
| Email Validator | `/tools/email-validator` | ✅ fatto |

---

## ⚙️ Convenzioni di Codice

### Backend (PHP / Laravel)
- **PSR-12** per la formattazione del codice.
- Ogni tool ha una **classe PHP dedicata** in `app/Tools/{ToolName}/{ToolName}.php`.
  - La classe contiene solo logica pura — zero dipendenze da Laravel o HTTP.
  - Il controller chiama la classe e ritorna una view Blade.
- Usare **Form Requests** per la validazione (mai validare nel controller).
- Nomi controller: `SubnetCalculatorController`, `CableSchemasController`, ecc.
- Nomi metodi: `index()` per la pagina, `calculate()` / `lookup()` per le chiamate htmx.
- Rotte con prefisso `/tools` e nome `tools.{nome-tool}`.
- Le risposte alle chiamate htmx restituiscono **partial Blade views** (non JSON).

```php
// Esempio struttura controller
class SubnetCalculatorController extends Controller
{
    public function index(): View
    {
        return view('tools.subnet-calculator');
    }

    public function calculate(SubnetRequest $request): View
    {
        $result = (new SubnetCalculator($request->validated()))->calculate();
        return view('tools.partials.subnet-result', compact('result'));
    }
}
```

### Frontend (Blade + Alpine.js + htmx)
- **Blade** per la struttura HTML e il rendering lato server.
- **Alpine.js** per interattività inline leggera (toggle, tab, stato locale UI).
- **htmx** per chiamate al server senza ricaricare la pagina (sostituisce axios/fetch).
- **Zero JavaScript custom** — niente file `.js` scritti a mano salvo eccezioni motivate.
- Le risposte htmx sono partial Blade views in `resources/views/tools/partials/`.
- Tailwind via CDN Play (`https://cdn.tailwindcss.com`) — niente build step.

```html
<!-- Esempio form con htmx -->
<form hx-post="{{ route('tools.subnet-calculator.calculate') }}"
      hx-target="#result"
      hx-swap="innerHTML">
    @csrf
    <input type="text" name="ip" placeholder="192.168.1.0" x-data>
    <input type="number" name="cidr" min="0" max="32">
    <button type="submit">{{ __('tools.subnet_calculator.calculate') }}</button>
</form>

<div id="result"></div>
```

### Internazionalizzazione (i18n)
- Lingua default: **Italiano (`it`)**.
- Ogni stringa UI deve avere la chiave in ENTRAMBI i file lingua — mai lasciarne una incompleta.
- Struttura chiavi: `tools.{nome_tool}.{chiave}` per i tool, `ui.{chiave}` per la UI globale.
- La lingua va salvata nella **sessione Laravel** (`session('locale')`).
- Il middleware `SetLocale` legge la sessione e chiama `App::setLocale()`.
- Rotta cambio lingua: `POST /language/{locale}` → `LanguageController@switch`.
- Nei template Blade usare sempre `__('tools.nome.chiave')` o `@lang(...)`.

```php
// lang/it/tools.php — esempio
return [
    'subnet_calculator' => [
        'title'          => 'Calcolatore Subnet',
        'description'    => 'Calcola subnet, host range e broadcast da IP e CIDR.',
        'input_ip'       => 'Indirizzo IP',
        'input_cidr'     => 'Prefisso CIDR',
        'calculate'      => 'Calcola',
        'result_network' => 'Indirizzo di Rete',
        'result_broadcast'=> 'Broadcast',
        'result_hosts'   => 'Host utilizzabili',
    ],
];
```

---

## 🧪 Testing

- Ogni tool **deve avere almeno un test feature** in `tests/Feature/Tools/`.
- I test coprono: input valido, input non valido, risposta HTTP corretta, presenza testo nella view.
- Nessun database → nessuna migration nei test, usare `WithoutMiddleware` solo se necessario.
- Eseguire: `docker compose exec app php artisan test`
- Prima di implementare un tool complesso, **scrivere prima i test** (TDD).

```php
class SubnetCalculatorTest extends TestCase
{
    public function test_shows_calculator_page(): void
    {
        $this->get(route('tools.subnet-calculator.index'))
             ->assertOk()
             ->assertSee(__('tools.subnet_calculator.title'));
    }

    public function test_calculates_subnet_correctly(): void
    {
        $this->post(route('tools.subnet-calculator.calculate'), [
            'ip'   => '192.168.1.0',
            'cidr' => 24,
        ])->assertOk()->assertSee('192.168.1.255'); // broadcast atteso
    }
}
```

---

## 🚀 Workflow di Sviluppo

### Per aggiungere un nuovo tool:
1. Creare la classe logica: `app/Tools/{Nome}/{Nome}.php`
2. Creare Form Request: `app/Http/Requests/Tools/{Nome}Request.php`
3. Creare il controller: `app/Http/Controllers/Tools/{Nome}Controller.php`
4. Aggiungere le rotte in `routes/web.php`
5. Aggiungere le traduzioni in `lang/it/tools.php` e `lang/en/tools.php`
6. Creare la view principale: `resources/views/tools/{nome}.blade.php`
7. Creare la partial view: `resources/views/tools/partials/{nome}-result.blade.php`
8. Aggiungere il link al menu in `resources/views/layouts/app.blade.php`
9. Scrivere il test: `tests/Feature/Tools/{Nome}Test.php`
10. Aggiornare la tabella dei tool in questo `CLAUDE.md` (Status → ✅)

### Comandi utili:
```bash
# Avviare l'ambiente
docker compose up -d

# Creare un controller
docker compose exec app php artisan make:controller Tools/SubnetCalculatorController

# Creare un Form Request
docker compose exec app php artisan make:request Tools/SubnetRequest

# Eseguire i test
docker compose exec app php artisan test

# Eseguire un singolo test
docker compose exec app php artisan test --filter=SubnetCalculator

# Accedere alla shell del container
docker compose exec app sh

# Vedere i log
docker compose logs -f app
```

---

## ⚠️ Regole Generali per Claude Code

1. **Plan first, implement after.** Su task complessi, mostra sempre il piano prima di scrivere codice.
2. **Un tool alla volta.** Non implementare più tool in una singola sessione.
3. **Niente Node / npm / Vite** — CSS e JS esclusivamente via CDN.
4. **Niente database** — i tool sono stateless, nessuna migration da creare.
5. **Non modificare** `docker-compose.yml` o i Dockerfile senza esplicita richiesta.
6. **Sempre aggiornare** le traduzioni IT e EN insieme — mai lasciare una lingua incompleta.
7. **Rispettare la struttura** delle cartelle definita sopra — non creare cartelle alternative.
8. **Niente logica nel frontend** — Alpine.js gestisce solo UI, htmx solo il trasporto, il calcolo è sempre PHP.
9. **Le risposte htmx sono Blade partials** — mai restituire JSON da un controller tool.
10. **Verificare i test** dopo ogni implementazione: `php artisan test --filter={NomeTool}`.

---

## 📝 Note e Decisioni Architetturali

- **Perché Blade + htmx invece di Vue/React?** Zero build step, zero Node, bundle JS < 30KB totali. Per tool stateless è più che sufficiente e mantiene il container leggero.
- **Perché Alpine.js?** Gestisce lo stato UI locale (es. tab attivo, errori inline) con attributi HTML — niente file JS separati.
- **Perché htmx?** Permette richieste POST/GET con sostituzione DOM senza scrivere fetch/axios, mantenendo la logica nel controller PHP.
- **Perché nessun database?** Tutti i tool calcolano on-the-fly — nessun dato da persistere. Aggiungere SQLite solo se in futuro serve (es. preferenze utente, storico).
- **Perché sessione per la lingua?** Funziona lato server senza JavaScript, compatibile con htmx, e non richiede store frontend.
- **Strumenti come Ping/Traceroute** usano `exec()` PHP con whitelist di comandi permessi — mai passare input utente direttamente a exec().
- **Tailwind CDN Play** è accettabile per un tool interno. Se le performance diventano un problema, valutare il Tailwind CLI standalone (niente Node richiesto).

---

*Ultimo aggiornamento: stack semplificato — Blade + Alpine.js + htmx, nessun DB, 2 container.*
