<?php

use App\Models\User;

uses()->group('models');

// it('check if admin user is admin', function() {
//     $adminUser = User::factory()->admin()->create();
//     expect($adminUser->isAdmin())->toBeTrue();

//     $regularUser = User::factory()->regular()->create();
//     expect($regularUser->isAdmin())->toBeFalse();
// });

// it('check if author user is author', function() {
//     $authorUser = User::factory()->author()->create();
//     expect($authorUser->isAuthor())->toBeTrue();

//     $regularUser = User::factory()->regular()->create();
//     expect($regularUser->isAuthor())->toBeFalse();
// });

// it('check if regular user is regular', function() {
//     $regularUser = User::factory()->regular()->create();
//     expect($regularUser->isRegular())->toBeTrue();

//     $adminUser = User::factory()->admin()->create();
//     expect($adminUser->isRegular())->toBeFalse();
// });

// it('has a scope to retrieve all admin users', function() {
//     $adminUser = User::factory()->admin()->create();

//     $adminUsers = User::whereAdmin()->get();

//     expect($adminUsers)->toHaveCount(1);
//     expect($adminUsers[0]->id)->toEqual($adminUser->id);
// });

// it('has a scope to retrieve all author users', function() {
//     $authorUser = User::factory()->author()->create();

//     $authorUsers = User::whereAuthor()->get();

//     expect($authorUsers)->toHaveCount(1);
//     expect($authorUsers[0]->id)->toEqual($authorUser->id);
// });

// it('has a scope to retrieve all regular users', function() {
//     $regularUser = User::factory()->regular()->create();

//     $regularUsers = User::whereRegular()->get();

//     expect($regularUsers)->toHaveCount(1);
//     expect($regularUsers[0]->id)->toEqual($regularUser->id);
// });
