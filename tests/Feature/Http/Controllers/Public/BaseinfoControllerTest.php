<?php

use App\Models\Baseinfo;
use App\Http\Controllers\Public\BaseinfoController;


uses()->group('public');

/* ------------------------------ @index method ----------------------------- */
it('renders the baseinfo page with data', function() {
    Baseinfo::factory()->create();
    $infoAddress = Baseinfo::first()->address;

    $response = $this->get(action([BaseinfoController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All infos:');
    $response->assertSee($infoAddress);
});
