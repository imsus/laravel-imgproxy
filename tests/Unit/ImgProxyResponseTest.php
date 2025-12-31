<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxyResponse;

describe('ImgProxyResponse', function () {
    $sampleUrl = 'http://localhost:8080/signature/width:300/height:200/aHR0cHM6Ly9wbGFjZWhvbGQuY28vNjAweDQwMC5qcGcu.jpg';

    describe('make', function () use ($sampleUrl) {
        it('creates a response from a URL', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);

            expect($response)->toBeInstanceOf(ImgProxyResponse::class);
            expect($response->getUrl())->toBe($sampleUrl);
        });

        it('has default status of 302', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);

            expect($response->getStatus())->toBe(302);
        });

        it('has empty headers by default', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);

            expect($response->getHeaders())->toBe([]);
        });
    });

    describe('redirect', function () use ($sampleUrl) {
        it('returns a RedirectResponse', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);
            $redirect = $response->redirect();

            expect($redirect)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
            expect($redirect->getTargetUrl())->toBe($sampleUrl);
        });

        it('allows custom status code', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);
            $redirect = $response->redirect(301);

            expect($redirect->getStatusCode())->toBe(301);
        });

        it('preserves custom headers', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);
            $redirect = $response->redirect(302, ['X-Custom' => 'value']);

            expect($redirect->headers->get('X-Custom'))->toBe('value');
        });
    });

    describe('stream', function () use ($sampleUrl) {
        it('returns a Response', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);
            $stream = $response->stream();

            expect($stream)->toBeInstanceOf(\Illuminate\Http\Response::class);
            expect($stream->getContent())->toBe($sampleUrl);
        });

        it('has text/plain content type', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);
            $stream = $response->stream();

            expect($stream->headers->get('Content-Type'))->toBe('text/plain');
        });
    });

    describe('getters', function () use ($sampleUrl) {
        it('returns correct URL', function () use ($sampleUrl) {
            $response = ImgProxyResponse::make($sampleUrl);

            expect($response->getUrl())->toBe($sampleUrl);
        });

        it('returns correct status', function () use ($sampleUrl) {
            $response = new ImgProxyResponse($sampleUrl, 301);

            expect($response->getStatus())->toBe(301);
        });

        it('returns correct headers', function () use ($sampleUrl) {
            $headers = ['X-Test' => 'value'];
            $response = new ImgProxyResponse($sampleUrl, 302, $headers);

            expect($response->getHeaders())->toBe($headers);
        });
    });
});
