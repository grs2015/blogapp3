<?php

use App\Models\Baseinfo;
use App\Http\Controllers\BaseinfoController;

uses()->group('BIC');

/* ------------------------------ @index method ----------------------------- */
it('renders the info page with infos data', function() {
    $info = Baseinfo::factory()->create();
    $infoAddress = Baseinfo::first()->address;

    $response = $this->get(action([BaseinfoController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All infos:');
    $response->assertSee($infoAddress);
});
