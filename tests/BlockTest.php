<?php
/**
 * @author Aaron Francis <aaron@hammerstone.dev|https://twitter.com/aarondfrancis>
 */

namespace Hammerstone\Torchlight\Tests;

use Hammerstone\Torchlight\Block;
use Hammerstone\Torchlight\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BlockTest extends BaseTest
{

    /** @test */
    public function it_dedents_code()
    {
        $block = Block::make();

        $code = <<<EOT
    echo 1;
    if (1) {
        return;
    }
EOT;

        $block->code($code);

        $dedented = $code = <<<EOT
echo 1;
if (1) {
    return;
}
EOT;

        $this->assertEquals($block->code, $dedented);
    }

    /** @test */
    public function it_right_trims()
    {
        $block = Block::make()->code('echo 1;      ');

        $this->assertEquals($block->code, 'echo 1;');
    }

    /** @test */
    public function you_can_set_your_own_id()
    {
        $block = Block::make('custom_id');

        $this->assertEquals($block->id(), 'custom_id');
    }

    /** @test */
    public function it_will_set_an_id()
    {
        $block = Block::make();

        $this->assertNotNull($block->id());
    }

    /** @test */
    public function hash_is_calculated()
    {
        $block = Block::make();

        $this->assertNotNull($hash = $block->hash());

        $block->code('new code');

        $this->assertNotEquals($hash, $hash = $block->hash());

        $block->theme('new theme');

        $this->assertNotEquals($hash, $hash = $block->hash());

        $block->language('new language');

        $this->assertNotEquals($hash, $hash = $block->hash());

        config()->set('torchlight.bust', 'new bust');

        $this->assertNotEquals($hash, $hash = $block->hash());

        // Hashes are stable if nothing changes.
        $this->assertEquals($hash, $block->hash());
    }

    /** @test */
    public function to_request_params_includes_required_info()
    {
        $block = Block::make('id');
        $block->code('new code');
        $block->theme('new theme');
        $block->language('new language');

        $this->assertEquals([
            'id' => 'id',
            'hash' => '494dc5134843e4f6fdcc838e8bcd7f7c',
            'language' => 'new language',
            'theme' => 'new theme',
            'code' => 'new code',
        ], $block->toRequestParams());
    }

    /** @test */
    public function default_theme_is_used()
    {
        config()->set('torchlight.theme', 'a new default');

        $block = Block::make('id');

        $this->assertEquals('a new default', $block->theme);

    }


}