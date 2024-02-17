<?php

declare (strict_types=1);

namespace FranIglesias\Golden;

use FranIglesias\Golden\Config\Namer;
use FranIglesias\Golden\Config\PSR4Namer;
use FranIglesias\Golden\Normalizer\JsonNormalizer;
use FranIglesias\Golden\Normalizer\Normalizer;
use FranIglesias\Golden\Reporter\PhpUnitReporter;
use FranIglesias\Golden\Reporter\Reporter;
use FranIglesias\Golden\Storage\FileSystemStorage;
use FranIglesias\Golden\Storage\Storage;
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
        $this->namer = new PSR4Namer();
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

    private function normalize($subject): string
    {
        // Scrubs should be applied here, after normalization
        return $this->normalizer->normalize($subject);
    }

    public function report(string $previous, string $normalized): string
    {
        return $this->reporter->report($previous, $normalized);
    }

    public function approvalFlow(string $name, string $normalized): void
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

    public function verifyFlow(string $name, string $normalized): void
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
