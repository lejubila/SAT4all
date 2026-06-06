<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_home_page_renders_in_italian_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(__('ui.app_name'))
            ->assertSee('Strumenti di rete e riferimenti tecnici per sistemisti');
    }

    public function test_language_switch_persists_and_changes_locale(): void
    {
        // Passa all'inglese
        $this->post('/language/en')->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('Networking tools and technical references for sysadmins');

        // Torna all'italiano
        $this->post('/language/it')->assertRedirect();

        $this->get('/')
            ->assertSee('Strumenti di rete e riferimenti tecnici per sistemisti');
    }

    public function test_unsupported_locale_is_ignored(): void
    {
        $this->post('/language/de')->assertRedirect();

        // Resta sulla lingua di default (italiano)
        $this->get('/')
            ->assertSee('Strumenti di rete e riferimenti tecnici per sistemisti');
    }
}
