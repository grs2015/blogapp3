<?php

use App\Models\Baseinfo;
use Illuminate\Http\UploadedFile;
use function Spatie\PestPluginTestTime\testTime;
use App\Http\Controllers\Admin\BaseinfoController;

uses()->group('admin');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the baseinfo page with tags data', function() {
    loginAsAdmin();
    Baseinfo::factory()->create();
    $infoAddress = Baseinfo::first()->address;

    $response = $this->get(action([BaseinfoController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All infos:');
    $response->assertSee($infoAddress);

    loginAsSuperAdmin();

    $response = $this->get(action([BaseinfoController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All infos:');
    $response->assertSee($infoAddress);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create baseinfo form', function() {
    $this->get(action([BaseinfoController::class, 'create']))->assertSee('Form for baseinfo creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect on storing', function() {
    $baseData = [
        'title' => 'New info',
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.baseinfo.index'));
});

it('checks the session error when validation fails on storing', function() {
    $baseData = [
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);

    $response->assertSessionHasErrors();
});

it('checks the stored info in database', function() {
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
    $this->withoutExceptionHandling();
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

    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('baseinfos', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);
});

/* ------------------------------ @show method ------------------------------ */
it('renders single info entry by given ID', function() {
    $base = Baseinfo::factory()->create();

    $response = $this->get(action([BaseinfoController::class, 'show'], ['baseinfo' => $base->id]));

    $response->assertSee($base->title);
    $response->assertSee($base->content);
    $response->assertSee($base->address);
    $response->assertDontSee($base->summary);
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for info entry by given ID', function() {
    $base = Baseinfo::factory()->create();

    $response = $this->get(action([BaseinfoController::class, 'edit'], ['baseinfo' => $base->id]));

    $response->assertSee($base->title);
    $response->assertSee($base->content);
    $response->assertSee($base->address);
    $response->assertDontSee($base->summary);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the validation and redirect on updating', function() {
    $info = Baseinfo::factory()->create();

    $baseData = [
        'title' => 'New info',
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->put(action([BaseinfoController::class, 'update'], ['baseinfo' => $info->id]), $baseData);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([BaseinfoController::class, 'edit'], ['baseinfo' => $info->id]));
});

it('checks the updated info in database', function() {
    $info = Baseinfo::factory()->create();
    $baseData = [
        'title' => 'New info',
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];

    $response = $this->put(action([BaseinfoController::class, 'update'], ['baseinfo' => $info->id]), $baseData);

    $this->assertDatabaseHas('baseinfos', ['title' => 'New info']);
    $response->assertStatus(302);
    $response->assertRedirect(action([BaseinfoController::class, 'edit'], ['baseinfo' => $info->id]));
});

it('checks the hero-image upload and its url resides in database after info updating', function() {
    // Arrange #1
    testTime()->freeze('2022-01-01 00:00:00');
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $baseData = [
        'title' => 'New info',
        'hero_image' => $file,
        'meta_title' => 'Meta information',
        'content' => 'Info content',
    ];
    // Action #1
    $response = $this->post(action([BaseinfoController::class, 'store']), $baseData);
    // Assertion #1
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('baseinfos', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);

    // Arrange #2
    testTime()->addHour();
    $file = UploadedFile::fake()->image('test.jpg');
    $baseData = [
        'title' => 'Newest info',
        'hero_image' => $file,
        'meta_title' => 'New meta information',
        'content' => 'New info content',
    ];
    $info = Baseinfo::first();
    // Action #2
    $response = $this->put(action([BaseinfoController::class, 'update'], ['baseinfo' => $info->id]), $baseData);
    // Assertion #2
    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-01-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-01-00-00-test.jpg');
    $urlEntry = 'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseMissing('baseinfos', [
        'hero_image' => $urlEntry
    ]);
    $urlEntry = 'uploads/HiRes-2022-01-01-01-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-01-00-00-test.jpg';
    $this->assertDatabaseHas('baseinfos', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry', function() {
    $info = Baseinfo::factory()->create();

    $response = $this->delete(action([BaseinfoController::class, 'destroy'], ['baseinfo' => $info->id]));

    $response->assertRedirect(route('admin.baseinfo.index'));
    $this->assertModelMissing($info);
    $this->assertDatabaseMissing('baseinfos', $info->toArray());
});

