<?php

declare (strict_types=1);

namespace Golden;

use Golden\Nomenclature\DefaultNomenclature;
use Golden\Nomenclature\Nomenclature;
use Golden\Normalizer\JsonNormalizer;
use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\TestCase;


trait Golden
{
    private Storage $storage;
    private JsonNormalizer $jsonNormalizer;
    private Nomenclature $nomenclature;


    private function init(): void
    {
        if (!isset($this->storage)) {
            $this->registerStorage(new MemoryStorage());
        }
        $this->jsonNormalizer = new JsonNormalizer();
        $this->nomenclature = new DefaultNomenclature();
    }

    public function registerStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    public function verify($subject): void
    {
        $this->init();

        $normalized = $this->normalize($subject);

        $name = $this->snapshotName();

        if (!$this->storage->exists($name)) {
            $this->storage->keep($name, $normalized);
        }

        $snapshot = $this->storage->retrieve($name);

        /* @var $this TestCase|Golden */
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

    public function snapshotName(): string
    {
        /* @var $this TestCase|Golden */
        return $this->nomenclature->name($this);
    }
}
