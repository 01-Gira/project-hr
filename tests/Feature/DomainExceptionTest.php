<?php

use App\Exceptions\DomainException;
use Illuminate\Support\Facades\Route;

/*
| The exception convention required by PHASE 0. Phase 4's scanner depends on it
| to report specific causes rather than a generic failure (08 §7).
*/

beforeEach(function () {
    $this->exception = new class('internal detail for logs') extends DomainException
    {
        public function userMessage(): string
        {
            return 'QR expired.';
        }

        /**
         * @return array<string, mixed>
         */
        public function context(): array
        {
            return ['expired_at' => '2026-09-07T08:00:00Z'];
        }
    };
});

test('a domain exception separates the log message from the user message', function () {
    expect($this->exception->getMessage())->toBe('internal detail for logs')
        ->and($this->exception->userMessage())->toBe('QR expired.');
});

test('it defaults to 422, since the request was valid but a rule refused it', function () {
    expect($this->exception->statusCode())->toBe(422);
});

test('it renders as json with the user message and context', function () {
    $exception = $this->exception;

    Route::get('/_test/domain-exception', function () use ($exception) {
        throw $exception;
    })->middleware('web');

    $this->getJson('/_test/domain-exception')
        ->assertStatus(422)
        ->assertJson([
            'message' => 'QR expired.',
            'context' => ['expired_at' => '2026-09-07T08:00:00Z'],
        ]);
});

test('the internal message is never leaked to the client', function () {
    $exception = $this->exception;

    Route::get('/_test/domain-exception-leak', function () use ($exception) {
        throw $exception;
    })->middleware('web');

    $this->getJson('/_test/domain-exception-leak')
        ->assertStatus(422)
        ->assertDontSee('internal detail for logs');
});
