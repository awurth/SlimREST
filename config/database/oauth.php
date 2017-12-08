<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create('oauth_clients', function (Blueprint $table) {
    $table->string('client_id', 80);
    $table->string('client_secret', 80)->nullable();
    $table->string('redirect_uri', 2000)->nullable();
    $table->string('grant_types', 80)->nullable();
    $table->string('scope', 4000)->nullable();
    $table->string('user_id', 80)->nullable();

    $table->primary('client_id');
});

Manager::schema()->create('oauth_access_tokens', function (Blueprint $table) {
    $table->string('access_token', 40);
    $table->string('client_id', 80);
    $table->string('user_id', 80)->nullable();
    $table->timestamp('expires');
    $table->string('scope', 4000)->nullable();

    $table->primary('access_token');
});

Manager::schema()->create('oauth_authorization_codes', function (Blueprint $table) {
    $table->string('authorization_code', 40);
    $table->string('client_id', 80);
    $table->string('user_id', 80)->nullable();
    $table->string('redirect_uri', 2000)->nullable();
    $table->timestamp('expires');
    $table->string('scope', 4000)->nullable();
    $table->string('id_token', 1000)->nullable();

    $table->primary('authorization_code');
});

Manager::schema()->create('oauth_refresh_tokens', function (Blueprint $table) {
    $table->string('refresh_token', 40);
    $table->string('client_id', 80);
    $table->string('user_id', 80)->nullable();
    $table->timestamp('expires');
    $table->string('scope', 4000)->nullable();

    $table->primary('refresh_token');
});

Manager::schema()->create('oauth_scopes', function (Blueprint $table) {
    $table->string('scope', 80);
    $table->boolean('is_default')->nullable();

    $table->primary('scope');
});

Manager::schema()->create('oauth_jwt', function (Blueprint $table) {
    $table->string('client_id', 80);
    $table->string('subject', 80)->nullable();
    $table->string('public_key', 2000);
});
