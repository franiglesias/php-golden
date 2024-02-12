<?php

declare (strict_types=1);

namespace Golden;

use Golden\Normalizer\JsonNormalizer;
use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;


trait Golden
{
    private Storage $storage;
    private JsonNormalizer $jsonNormalizer;


    private function init(): void
    {
        if (!isset($this->storage)) {
            $this->registerStorage(new MemoryStorage());
        }
        $this->jsonNormalizer = new JsonNormalizer();
    }

    public function registerStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }


    public function verify($subject): void
    {
        $this->init();

        $normalized = $this->normalize($subject);

        if (!$this->storage->exists($this->name())) {
            $this->storage->keep($this->name(), $normalized);
        }

        $snapshot = $this->storage->retrieve($this->name());
        $this->assertEquals($snapshot, $normalized);
    }

    private function normalize($subject): string
    {
        return $this->json_normalize($subject);
    }

    public function json_normalize($subject): string
    {
        return $this->jsonNormalizer->normalize($subject);
    }
}
