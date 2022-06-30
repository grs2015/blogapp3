<?php

use App\Models\Baseinfo;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\BaseinfoController;
use function Spatie\PestPluginTestTime\testTime;

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

/* ----------------------------- @create method ----------------------------- */
it('renders create baseinfo form', function() {
    $this->get('/baseinfo/create')->assertSee('Form for baseinfo creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $baseData = [
        'title' => 'New info',
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('baseinfo.index'));
});

it('checks the session error when validation fails', function() {
    $baseData = [
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    $response->assertSessionHasErrors();
});

it('checks the stored post info in database', function() {
    $baseData = [
        'title' => 'New info',
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('baseinfos', ['title' => 'New info']);
});

it('checks the hero-image upload and its url resides in database after info storing', function() {
    testTime()->freeze('2022-01-01 00:00:00');
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $baseData = [
        'title' => 'New info',
        'hero_image' => $file,
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test.jpg');
    $this->assertDatabaseHas('baseinfos', [
        'hero_image' => 'uploads/2022-01-01-00-00-00-test.jpg'
    ]);
    $response->assertStatus(302);
});
