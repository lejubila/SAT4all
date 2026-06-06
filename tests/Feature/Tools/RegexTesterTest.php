<?php

namespace Tests\Feature\Tools;

use App\Tools\RegexTester\RegexTester;
use Tests\TestCase;

class RegexTesterTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.regex-tester.index'))
            ->assertOk()
            ->assertSee(__('tools.regex_tester.title'));
    }

    public function test_empty_pattern_returns_idle(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => '',
            'subject' => 'hello world',
        ])->assertOk()->assertSee(__('tools.regex_tester.idle'));
    }

    public function test_basic_match(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => '\d+',
            'flags'   => '',
            'subject' => 'abc 123 def 456',
        ])->assertOk()->assertSee('123')->assertSee('456');
    }

    public function test_no_matches_returns_zero_count(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => 'xyz',
            'flags'   => '',
            'subject' => 'hello world',
        ])->assertOk()->assertSee(__('tools.regex_tester.match_count_zero'));
    }

    public function test_invalid_pattern_returns_error(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => '[unclosed',
            'flags'   => '',
            'subject' => 'test',
        ])->assertOk()->assertSee(__('tools.regex_tester.error_invalid'));
    }

    public function test_case_insensitive_flag(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => 'hello',
            'flags'   => 'i',
            'subject' => 'HELLO WORLD',
        ])->assertOk()->assertSee('HELLO');
    }

    public function test_capture_groups_shown(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern' => '(\w+)@(\w+)',
            'flags'   => '',
            'subject' => 'user@example',
        ])->assertOk()->assertSee('user')->assertSee('example');
    }

    public function test_replacement_result_shown(): void
    {
        $this->post(route('tools.regex-tester.test'), [
            'pattern'     => '\d+',
            'flags'       => '',
            'subject'     => 'Call 12345 now',
            'replacement' => '[NUM]',
        ])->assertOk()->assertSee('[NUM]');
    }

    public function test_class_basic_match(): void
    {
        $tester = new RegexTester();
        $result = $tester->test('\d+', '', 'abc 42 xyz');
        $this->assertTrue($result['valid']);
        $this->assertSame(1, $result['match_count']);
        $this->assertSame('42', $result['matches'][0]['value']);
    }

    public function test_class_invalid_pattern(): void
    {
        $tester = new RegexTester();
        $result = $tester->test('[bad', '', 'test');
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['error']);
    }

    public function test_class_replace(): void
    {
        $tester = new RegexTester();
        $result = $tester->replace('\d+', '', 'abc 42 xyz', 'NUM');
        $this->assertSame('abc NUM xyz', $result);
    }

    public function test_class_capture_groups(): void
    {
        $tester  = new RegexTester();
        $result  = $tester->test('(\w+)@(\w+)', '', 'user@host');
        $groups  = $result['matches'][0]['groups'];
        $this->assertCount(2, $groups);
        $this->assertSame('user', $groups[0]['value']);
        $this->assertSame('host', $groups[1]['value']);
    }
}
