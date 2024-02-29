<?php

declare (strict_types=1);

namespace Golden;

use Golden\Config\PathFinder;
use Golden\Config\StandardPathFinder;
use Golden\Master\Combinations;
use Golden\Master\Runner;
use Golden\Normalizer\JsonNormalizer;
use Golden\Normalizer\Normalizer;
use Golden\Normalizer\Scrubber\Scrubber;
use Golden\Reporter\PhpUnitReporter;
use Golden\Reporter\Reporter;
use Golden\Storage\FileSystemStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;


trait Golden
{
    private Storage $storage;
    private Normalizer $normalizer;
    private Config $config;
    private PathFinder $namer;
    private Reporter $reporter;


    private function init(): void
    {
        if (!isset($this->storage)) {
            $this->registerStorage(new FileSystemStorage());
        }
        $this->normalizer = new JsonNormalizer();
        if (!isset($this->config)) {
            $this->config = new Config();
        }
        $this->namer = new StandardPathFinder();
        $this->reporter = new PhpUnitReporter();
    }

    public function registerStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    public function verify($subject, callable ...$options): void
    {
        $this->init();

        $config = clone($this->config);

        foreach ($options as $option) {
            $option($config);
        }

        $normalized = $this->normalize($subject, ...$config->scrubbers());

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

    public function defaults(callable ...$options)
    {
        $this->init();

        foreach ($options as $option) {
            $option($this->config);
        }

        $this->config->setSnapshot("");
    }

    private function normalize($subject, Scrubber ...$scrubbers): string
    {
        $normalized = $this->normalizer->normalize($subject);
        foreach ($scrubbers as $scrubber) {
            $normalized = $scrubber->clean($normalized);
        }
        return $normalized;
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
        assertEquals($snapshot, $normalized);
    }
}

