<?php

declare (strict_types=1);

namespace Golden;

use Golden\Config\Namer;
use Golden\Config\StandardNamer;
use Golden\Master\Combination;
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

    public function master(callable $sut, array $values, callable ...$options)
    {
        $combinations = $this->prepareCombinations($values);

        $subject = [];
        foreach ($combinations as $key => $combination) {
            $subject[] = new Combination(
                $key + 1,
                $combination,
                $sut(...$combination));
        }

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

    public function prepareCombinations(array $values): array
    {
        // generate combinations
        $result = [[]];
        foreach ($values as $parameter) {
            $tmp = [];
            foreach ($parameter as $value) {
                foreach ($result as $combination) {
                    $t = $combination;
                    $t[] = $value;
                    $tmp[] = $t;
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}


/*

func Generate(arr [][]any) [][]any {
	// Return empty slice if there is nothing to combine
	if len(arr) == 0 {
		return [][]any{}
	}

	// Result will be a slice of slices holding each parameters combination
	result := [][]any{{}}

	// traverse the slice of parameters value slice
	for _, parameter := range arr {
		var temp [][]any
		// traverse the values in this parameter value slice
		for _, value := range parameter {
			// append the value to each combination
			for _, combination := range result {
				temp = append(temp, append(combination, value))
			}
		}
		// update result
		result = temp
	}

	return result
}

 */
