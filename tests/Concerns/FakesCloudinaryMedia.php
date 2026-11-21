<?php

namespace Tests\Concerns;

use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Http;
use Mockery;

trait FakesCloudinaryMedia
{
    protected function fakeCloudinaryMedia(bool $fakeHttp = true): void
    {
        $url = 'cloudinary://test-key:test-secret@test-cloud';
        config(['filesystems.disks.cloudinary.url' => $url]);

        $uploadApi = Mockery::mock(UploadApi::class);
        $uploadApi->shouldReceive('upload')
            ->zeroOrMoreTimes()
            ->andReturnUsing(static fn (string $source, array $options) => new ApiResponse([
                'public_id' => $options['public_id'],
            ], []));
        $uploadApi->shouldReceive('destroy')
            ->zeroOrMoreTimes()
            ->andReturn(new ApiResponse(['result' => 'ok'], []));

        $client = new class($url, $uploadApi) extends Cloudinary
        {
            public function __construct(string $url, private UploadApi $fakeUploadApi)
            {
                parent::__construct($url);
            }

            public function uploadApi(): UploadApi
            {
                return $this->fakeUploadApi;
            }
        };

        $this->app->instance(Cloudinary::class, $client);
        if ($fakeHttp) {
            Http::fake([
                'https://res.cloudinary.com/*' => Http::response('test image bytes', 200, [
                    'Content-Type' => 'image/png',
                ]),
            ]);
        }
    }
}
