<?php

declare (strict_types=1);

namespace Golden;

use Golden\Config\Namer;
use Golden\Config\PSR4Namer;
use Golden\Normalizer\JsonNormalizer;
use Golden\Normalizer\Normalizer;
use Golden\Storage\FileSystemStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\TestCase;


trait Golden
{
    private Storage $storage;
    private Normalizer $normalizer;
    private Config $config;
    private Namer $namer;


    private function init(): void
    {
        if (!isset($this->storage)) {
            $this->registerStorage(new FileSystemStorage());
        }
        $this->normalizer = new JsonNormalizer();
        $this->config = new Config();
        $this->namer = new PSR4Namer();
    }

    public function registerStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    public function verify($subject, callable ...$options): void
    {
        $this->init();

        $config = $this->config;

        foreach ($options as $option) {
            $option($config);
        }

        $normalized = $this->normalize($subject);

        $name = $config->name($this, $this->namer);

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
        return $this->normalizer->normalize($subject);
    }
}
