<?php

declare (strict_types=1);

namespace Golden;

use Golden\Config\Namer;
use Golden\Config\StandardNamer;
use Golden\Master\Combinations;
use Golden\Master\Runner;
use Golden\Normalizer\JsonNormalizer;
use Golden\Normalizer\Normalizer;
use Golden\Reporter\PhpUnitReporter;
use Golden\Reporter\Reporter;
use Golden\Storage\FileSystemStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\TestCase;


trait Golden
{
    private Storage $storage;
    private Normalizer $normalizer;
    private Config $config;
    private Namer $namer;
    private Reporter $reporter;


    private function init(): void
    {
        if (!isset($this->storage)) {
            $this->registerStorage(new FileSystemStorage());
        }
        $this->normalizer = new JsonNormalizer();
        $this->config = new Config();
        $this->namer = new StandardNamer();
        $this->reporter = new PhpUnitReporter();
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

        if ($config->approvalMode()) {
            $this->approvalFlow($name, $normalized);
        } else {
            $this->verifyFlow($name, $normalized);
        }
    }

    public function master(callable $sut, Combinations $params, callable ...$options)
    {
        $subject = (new Runner($sut))->execute($params->all());

        $options[] = extension(".snap.json");

        $this->verify($subject, ...$options);
    }

    private function normalize($subject): string
    {
        // Scrubs should be applied here, after normalization
        return $this->normalizer->normalize($subject);
    }

    private function report(string $previous, string $normalized): string
    {
        return $this->reporter->report($previous, $normalized);
    }

    private function approvalFlow(string $name, string $normalized): void
    {
        $previous = "";
        if ($this->storage->exists($name)) {
            $previous = $this->storage->retrieve($name);
        }

        $this->storage->keep($name, $normalized);
        // Show here approval mode message
        /* @var $this TestCase|Golden */
        $diff = $this->report($previous, $normalized);
        self::fail($diff);
    }

    private function verifyFlow(string $name, string $normalized): void
    {
        // Show here verify mode message
        if (!$this->storage->exists($name)) {
            $this->storage->keep($name, $normalized);
        }

        $snapshot = $this->storage->retrieve($name);

        /* @var $this TestCase|Golden */
        $this->assertEquals($snapshot, $normalized);
    }
}

