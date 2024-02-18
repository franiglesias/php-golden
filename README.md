# PHP Golden

A PHP library for snapshot 📸 testing.

[A port from](https://pkg.go.dev/github.com/franiglesias/golden)

**⚠️⚠️ Caution ⚠️⚠️**: This README is a Work in Progress. We are translating the original readme from Golden to the PHP version. There are parts that only apply to the Go version, features that are not yet in the PHP (Master is not yet).

[Cookbook: recipes and how-tos](doc/cookbook.md) (Not yet) ⛔️

- [TL;DR](#TLDR)
    - [🛠 Installation](#installation)
    - [📸 Basic Usage: Verify against an auto-generated snapshot](#basic-usage-verify-against-an-auto-generated-snapshot)
        - [How it works](#how-it-works)
    - [👍🏽 Basic Usage: Approval mode](#basic-usage-approval-mode)
        - [How it works](#how-it-works-1)
    - [🏆 Basic Usage: Golden Master mode](#basic-usage-golden-master-mode)
        - [How it works](#how-it-works-2)
- [What is Golden?](#what-is-golden)
- [Snapshot testing](#snapshot-testing)
- [Approval testing](#approval-testing)
    - [How to perform Approval Testing with **Golden**](#how-to-perform-approval-testing-with-golden)
- [Golden Master](#golden-master)
    - [How to do Golden Master testing with **Golden**](#how-to-do-golden-master-testing-with-golden)
        - [Wrap the subject under test](#wrap-the-subject-under-test)
        - [Prepare lists of values for each parameter](#prepare-lists-of-values-for-each-parameter)
        - [Putting it all together](#putting-it-all-together)
        - [Another example, managing errors:](#another-example-managing-errors)
- [Customizing the behavior](#customizing-the-behavior)
    - [Customize the snapshot name](#customize-the-snapshot-name)
    - [Customize the folder to store the snapshot](#customize-the-folder-to-store-the-snapshot)
    - [Customize the extension of the snapshot file](#customize-the-extension-of-the-snapshot-file)
- [Dealing with Non-Deterministic output](#dealing-with-non-deterministic-output)
    - [Replacing fields in Json Files with PathScrubbers](#replacing-fields-in-json-files-with-pathscrubbers)
    - [Caveats](#caveats)
    - [Create Custom Scrubbers](#create-custom-scrubbers)
    - [Predefined Scrubbers](#predefined-scrubbers)
    - [Options for Scrubbers](#options-for-scrubbers)
- [How snapshots are named](#how-snapshots-are-named)


## TL;DR

**Snapshot testing** is a technique in which the output of the subject under test is compared to a previously generated output, obtained by running the very same subject under test. The basic idea is to ensure that changes made to the code have not affected its behavior.

This is useful for:

* Test complex or large outputs such as objects, files, generated code, etc.
* Understand and put legacy code under test.
* Obtain high code coverage when starting to refactor legacy code or code that has no tests.

**Current Status**: v0.0. Not complete port yet, but it has support for Verify and Approval modes

**Roadmap/Pending features**:

* `snapshot()` option for naming a test
* Golden Master
* Scrubbers support
* In general, synchronize with the current features of the original Golden

For future releases:

* Ability and API to use custom reporters.
* Ability and API to use custom normalizers.
* Global options that apply to all tests.
* Better scoping of Scrubbers for JSON content, using paths.

**Usage advice**: Only experimental.

### Installation

Composer package installation via repository. Not yet published as package.

Add this key in your **composer.json** file.

```json
"repositories": [
    {
    "type": "vcs",
    "url": "https://github.com/franiglesias/php-golden.git"
    }
],
```

Now, you can require the package using the standard compose require.

```
composer require --dev "franiglesias/golden" "dev-main"
```

You can always update the library to get the last available version.

Take into account that we will publish as a package when all basic features are completed.

### Basic Usage: Verify against an auto-generated snapshot

A snapshot test is pretty easy to write. Capture the output of the subject under test in a variable and pass it to `$this->verify()`. `Golden` will take care of all. You can use any type that suits you well.

In PHP Golden is a Trait, so you have to declare that you are using the Golden trait in your PHPUnit test. That's all. Now, you have a `verify` method. 

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed());
    }
}
```

Sometimes, you could prefer to convert the output to `string` to have a snapshot more readable for a human.

#### How it works

The first time you run the test, a snapshot file will be generated at `__snapshots/TestSomething/test_speed_of_european_parrot.snap` in the same folder of the test. And the test **will pass**. See [How snapshots are named](#how-snapshots-are-named) to learn about how names are created in **Golden**.

The file will contain the output generated by the subject under test, provided that it can be serialized as JSON.

If the snapshot is ok for you, commit it with the code, so it can be used as comparison criteria in future runs and prevent regressions. If not, delete the file, make the changes needed in the code or tests, and run it again.

But, if you are not sure that the current output is correct, you can try the approval mode.

### Basic Usage: Approval mode

_Approval mode_ is useful when you are writing new code. In this mode, the snapshot is generated and updated but the test never passes. Why? You will need to inspect the snapshot until you are happy with it, and you, or a domain expert, _approve_ it.

Pass the option `waitApproval()` to run the test in approval mode.

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed(), waitApproval());
    }
}
```

Once you or the domain expert approves the snapshot, remove the `waitApproval()` option. That's all. The last generated snapshot will be used as a criterion.

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed());
    }
}
```

#### How it works

The first time you run the test, a snapshot file will be generated at `__snapshots/TestSomething/test_speed_of_european_parrot.snap` in the same package of the test. And the test **will not pass**. Subsequent runs of the test won't pass until you remove the `waitApproval()` option, even if there are no differences between the snapshot and the current output.

The file will contain the output generated by the subject under test, provided that it can be serialized as JSON.

If the snapshot is ok for you, remove the option `waitApproval()`, so it can be used as comparison criteria in future runs. If not, modify the code and run it again until the snapshot is fine.

### Basic Usage: Golden Master mode

**This section is not updated yet** 

Golden Master mode is useful when you want to generate a lot of tests combining different values of the subject under test parameters. It will generate all possible combinations, creating a detailed snapshot with all the results.

You will need to create a Wrapper function that exercises the subject under test managing both parameters and all return values, including errors. You will need to manage to return a `string` representation of the outcome of the SUT.

Here is an example testing a hypothetical `Division` function.

```go
func TestWithGoldenMaster(t *testing.T) {
    f := func(args ...any) any {
        result, err := Division(args[0].(float64), args[1].(float64))
        if err != nil {
            return err.Error()
        }
        return result
    }

    dividend := []any{1.0, 2.0}
    divisor := []any{0.0, -1.0, 1.0, 2.0}

    gld.Master(t, f, golden.Combine(dividend, divisor))
}
```

#### How it works

The first time you run the test, a snapshot file will be generated at `__snapshots/TestWithGoldenMaster.snap.json` in the same package of the test. This will be a JSON file with a description of the inputs and outputs of each generated test. The test itself will pass except if you use the `waitApproval()` option.

As a bonus, you can use GoldenMaster tests in Approval mode.

## What is Golden?

**Golden** is a library inspired by projects like [Approval Tests](https://approvaltests.com/). There are some other similar libraries out there, such as [Approval Tests](https://github.com/approvals/go-approval-tests), [Go-snaps](https://github.com/gkampitakis/go-snaps) or [Cupaloy](https://github.com/bradleyjkemp/cupaloy) that offer similar functionality.

So... Why reinvent the wheel?

First of all, why not? I was willing to start a little side project to learn and practice some Golang things for which I didn't find time or opportunity during the daily work. For example, creating a library for distribution, resolving some questions about managing state, creating friendly APIs, managing unknown types, etc.

Second. I found some limitations in the libraries I was using (Approval tests, most of the time) that made my work a bit uncomfortable. So, I started to look for alternatives. I wanted some more flexibility and customization. At the end of the day, I decided to create my library.

Golden for go is working pretty well for me, so I decided to port the library to PHP following similar principles.

## Snapshot testing

Snapshot testing is a testing technique that provides an alternative to assertion testing. In assertion testing, you compare the output of executing some unit of code with an expectation you have about it. For example, you could expect that the output would equal some value, or that it contains some text, etc.

Here you have a typical assertion for equality:

```php
$this->assertEqual("Expected", output)
```

This works very well for TDD and for testing simple outputs. However, it is tedious if you need to test complex objects, generated files, and other big outputs. Also, it is not always the best tool for testing code that you don't know well or that was not created with testing in mind.

In snapshot testing, instead, you first obtain and persist the output of the current behavior of the subject under test. This is what we call a _snapshot_. This provides us with a regression test for that piece of code. So, if you make some changes, you can be sure that the behavior is not affected by those changes.

And this is how you achieve that:

```php
$this->verify(subject)
```

As you can see, the main difference with the assertion is that we don't specify any expectations about the subject. `verify` will store the `subject` value in a file the first time it runs and will be used as the expected value to compare in subsequent runs.

**Golden** automates the snapshot creation process the first time the test is run, and uses that same snapshot as a criterion in subsequent runs. We call this workflow "Verification mode": the goal of the test is to verify that the output is the same as the first snapshot. As you can easily guess, this works as a regression test.

Snapshot testing is a very good first approach to put legacy code under test or to introduce tests in a codebase without tests. You don't need to know how that code works. You only need a way to capture the output of the existing code.

However, testing legacy or unknown code is not the only use case for snapshot testing.

Snapshot testing is a very good choice for testing complex objects or outputs such as generated HTML, XML, JSON, code, etc. Provided that you can create a file with a serialized representation of the response, you can compare the snapshot with subsequent executions of the same unit of code. Suppose you need to generate an API response with lots of data. Instead of trying to figure out how to check every possible field value, you generate a snapshot with the data. After that, you will be using that snapshot to verify the execution.

But this way of testing is weird for developing new features... How can I know that my generated output is correct at a given moment?

## Approval testing

Approval testing is a variation of snapshot testing. Usually, in snapshot testing, the first time you execute the test, a new snapshot is created and the test automatically passes. Then, you make changes to the code and use the snapshot to ensure that there are no behavioral changes. This is the default behavior of **Golden**, as you can see.

But when we are creating new features we don't want that behavior. We build the code and check the output from time to time. The output must be reviewed to ensure that it has the required content. In this situation, is preferable to not pass the test until the generated output is reviewed by an expert.

In Approval Testing, the first test execution is not automatically passed. Instead, the snapshot is created, but you should review and approve it explicitly. This step allows you to be sure that the output is what you want or what was required. You can show it to the business people, to your client, to the user of your API, or to whoever can review it. Once you get the approval of the snapshot and re-run the test, it will pass.

You could make changes and re-run the test all the times you need until you are satisfied with the output and get approval.

I think that Approval Testing was first introduced by [Llewellyn Falco](https://twitter.com/llewellynfalco). You can [learn more about this technique on their website](https://approvaltests.com/), where you can find out how to approach development with it.

### How to perform Approval Testing with Golden

Imagine you are writing some code that generates a complex struct, JSON object, or another long and complex document. You need this object to be reviewed by a domain expert to ensure that it contains what is supposed to contain.

If you work like me, you probably will start by generating an empty object and adding different elements one at a time, running a test on each iteration to be sure how things are going. Using **Golden** means using:

```php
$this->verify(theOutput)
```

Don't forget to use the Golden trait in your test.

But if you work in "verification mode" you will have to delete every snapshot that is created when running the test. Instead of that, you can use the _approval mode_. It is very easy: you simply have to pass the `waitApproval()` function as an option until the snapshot reflects exactly what you or the domain expert want.

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        // Verify waiting for approval so the test will always fail
        $this->verify($parrot->getSpeed(), waitApproval());
    }
}
```

This way, the test will always fail, and it will update the snapshot with the changes that happen in the output. This is because in _approval mode_ you don't want to allow the test to pass. You could consider this as an iterative way to build your snapshot: you make a change, run the test, and add some bit of content to the snapshot, until you are happy with what you get.

So, how can I declare that a snapshot was approved? Easy: simply change the test back to use the verification mode once you confirm that the last snapshot is correct by removing the `golden.WaitApproval()` option.

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        // Back to standard verification mode
        $this->verify($parrot->getSpeed());
    }
}
```


Other libraries require you to use some sort of external tool to rename or mark a snapshot as approved. **Golden** puts this distinction to the test itself. The fact that it fails, even after you've got the approval, allows you to remember that you will need to do something with the test.

Given the nature of the approval flow, the test will always fail even if the snapshot and the current output have no differences. This means that you are not adding more changes that modify the output. Usually, this can work as a rule of thumb to consider the snapshot as _approved_ and to remove de `waitApproval()` option.

## Golden Master

There is another variation of snapshot testing. **Golden Master** is a technique introduced by Michael Feathers for working with legacy code that you don't understand. Another way of naming this kind of testing is _Characterization testing_.

With this technique, you can achieve high coverage really fast, so you can be sure that refactoring will be safe because you always will know if the behavior of the code is broken due to a change you introduced. The best thing is that you don't need to understand the code to test it. Once you start refactoring things and abstracting code, it will be easier to introduce classic assertion testing or even TDD, and finally remove the Golden Master tests.

The technique requires the creation of a big batch of tests for the same unit of code, employing combinations of the parameters that you need to pass to such a unit. The original Approval Tests package includes Combinations, a library that helps you to generate those combinatorial tests.

There are several techniques to guess the best values you should use. You could, for example, study the code and look for significant values in conditionals with the help of a graphical coverage tool that shows you what parts of the code are executed or not depending on the values.

Once you complete the collection of possible values for each parameter, you will use the combination tools that will generate a batch of tests for you. The amount of tests is the product of multiplying the number of values per parameter. You can easily achieve tenths and even hundreds of tests for the same code unit.

As you have probably guessed, **Golden** takes its name from this technique... and because it starts with "Go". Anyway, I've just found that lots of packages are using the very same name, even in the std library.

### How to do Golden Master testing with **Golden**

A combinatorial test is a bit trickier than a plain snapshot. Not too much. The difficult part is to expose a simple API, but maybe **Golden** has something good to offer.

Let's see an example. Imagine that you have this function that creates a border around a title for console output. We don't need to know anything about the implementation. We should have enough with its signature.

```go
func Border(title string, part string, span int) string {
    width := span*2 + len(title) + 2
    top := strings.Repeat(part, width)
    body := part + strings.Repeat(" ", span) + title + strings.Repeat(" ", span) + part
    return top + "\n" + body + "\n" + top + "\n"
}
```

The signature has three parameters: the first two are strings and the third is an integer. We want to test the function with different values for each parameter.

#### Wrap the subject under test

The first thing we need is a wrapper function that takes any number of parameters of `any` type and returns something. We are going to pass this function to the `Master` method.

```go
f := func(args ...any) any {
    ...
}
```

The body of the wrapper must convert the received parameters back to the types required by the SUT. How to do this is totally up to you and depends on the concrete types. This example is pretty simple, and we only need to perform a type assertion. As you can see in the code, we receive the params as a slice of `any`, so you will identify the correspondent parameter by its position.

The wrapper function will return `any` type. But if you find problems with doing so, try to convert the output to `string`.

```go
f := func(args ...any) any {
    title := args[0].(string)
    part := args[1].(string)
    span := args[2].(int)
    
    return Border(title, part, span)
}
```

**What can I do if the SUT returns an error?** The best thing is to return the error using the `Error()` method. It should appear in the snapshot as is, so you will know what input combination generated it. Let's see an example. This is the function we want to test:

```go
func Division(a float64, b float64) (float64, error) {
    if b == 0 {
       return 0, errors.New("division by 0")
    }

    return a / b, nil
}
```

And this is the wrapper function we've created for it:

```go
f := func(args ...any) any {
    result, err := Division(args[0].(float64), args[1].(float64))
    if err != nil {
        return err.Error()
    }
    return result
}
```

**What can I do if the SUT doesn't return an output?** A suggested technique for this is to add some kind of logging facility that you can retrieve after exercising the subject under tests and retrieve that logged output.

#### Prepare lists of values for each parameter

The next thing we need to do is to prepare lists of values for each parameter. You will populate a slice of `any`, with all the values that you want to test. The slice is typed as `any` to allow any kind of data, but remember to use valid types for the signature of the SUT.

```go
titles := []any{"Example 1", "Example long enough", "Another thing"}
parts := []any{"-", "=", "*", "#"}
times := []any{0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
```

You will pass the collections of values with the convenience function `golden.Combine()`:

```go
golden.Combine(titles, parts, times)
```

**How should I choose the values?** It is an interesting question. In this example, it doesn't matter the specific values because the code only has one execution flow. In many cases, you can find interesting values in conditionals, that control the execution flow, allowing you to obtain the most code coverage executing all possible branches. The precedent and following values of those are also interesting. If you are unsure, you can even use a batch with several random values. Remember that once you set up the test, adding or removing values is very easy.

#### Putting it all together

And this is how you run a Golden Master test with **Golden**:

```go
func TestGoldenMaster(t *testing.T) {
    // define the wrapper function
    f := func(args ...any) any {
        title := args[0].(string)
        part := args[1].(string)
        span := args[2].(int)
        return Border(title, part, span)
    }
    
    // define the input values to combine
    titles := []any{"Example 1", "Example long enough", "Another thing"}
    parts := []any{"-", "=", "*", "#"}
    times := []any{0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10}

    // run the test
    golden.Master(t, f, golden.Combine(titles, parts, times))
}
```

This example will generate 132 tests: 3 titles * 4 parts * 11 times.

`Master` method will invoke `Verify` under the hood, using the result of executing all the combinations as the subject to create the snapshot. This is a very special snapshot, by the way. First of all, it is a JSON file containing an array of JSON objects, each of them representing one example. Like this:


```json
[
  {
    "Id": 1,
    "Params": "Example 1, -, 0",
    "Output": "-----------\n-Example 1-\n-----------\n"
  },
  {
    "Id": 2,
    "Params": "Example long enough, -, 0",
    "Output": "---------------------\n-Example long enough-\n---------------------\n"
  },
  {
    "Id": 3,
    "Params": "Another thing, -, 0",
    "Output": "---------------\n-Another thing-\n---------------\n"
  },
  
]
```

I think this will help you to understand the snapshot, identify easily interesting cases, and even post-process the result if you need to.

#### Another example, managing errors:

This is a test in which we manage the error that can be returned by the SUT:

```go
func TestManagingError(t *testing.T) {
    f := func(args ...any) any {
        result, err := Division(args[0].(float64), args[1].(float64))
        if err != nil {
            return err.Error()
        }
        return result
    }

    dividend := []any{1.0, 2.0}
    divisor := []any{0.0, -1.0, 1.0, 2.0}

    gld.Master(t, f, golden.Combine(dividend, divisor))
}
```

Take a look at the first records of the snapshot. The error message appears as the output of the corresponding combination.

```json
[
  {
    "Id": 1,
    "Params": "1, 0",
    "Output": "division by 0"
  },
  {
    "Id": 2,
    "Params": "2, 0",
    "Output": "division by 0"
  },
  {
    "Id": 3,
    "Params": "1, -1",
    "Output": -1
  },
  {
    "Id": 4,
    "Params": "2, -1",
    "Output": -2
  },
  {
    "Id": 5,
    "Params": "1, 1",
    "Output": 1
  },
  {
    "Id": 6,
    "Params": "2, 1",
    "Output": 2
  },
  {
    "Id": 7,
    "Params": "1, 2",
    "Output": 0.5
  },
  {
    "Id": 8,
    "Params": "2, 2",
    "Output": 1
  }
]
```

## Customizing the behavior

You can pass any combination of the following options in any of the test modes.

### Customize the snapshot name

You can customize the snapshot name, by passing the option `snapshot()`:

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed(), snapshot("european_snapshot"));
    }
}
```

This will generate the snapshot in `__snapshots/ParrotTest/european_snapshot.snap` in the same folder of the test.

This is useful if you need:

* More than one snapshot in the same test
* Use an externally generated file as a snapshot. For example, if you want your code to replicate the output of another system, provided that you have an example. Put the file inside the `__snapshots` folder.

### Customize the folder to store the snapshot

You can customize the snapshot name, by passing the option `folder()`:

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed(), folder("__parrots"));
    }
}
```

This will generate the snapshot in `__parrots/ParrotTest/test_speed_of_european_parrot.snap` in the same folder as the test.

You can use both options at the same time:

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed(), snapshot("european_snapshot"), folder("__parrots"));
    }
}
```


This will generate the snapshot in `__parrots/ParrotTest/european_snapshot.snap` in the same package of the test.

### Customize the extension of the snapshot file

You can customize the snapshot name, by passing `extension()`:

```php
class ParrotTest extends TestCase
{
    use Golden;
    
    public function testSpeedOfEuropeanParrot(): void
    {
        $parrot = $this->getParrot(ParrotTypeEnum::EUROPEAN, 0, 0, false);
        $this->verify($parrot->getSpeed(), extension('.data'));
    }
}
```


This will generate the snapshot in `__snapshots/ParrotTest/test_speed_of_european_parrot.data` in the same package of the test.

This option is useful if your snapshot can be files of a certain type, like CSV, JSON, HTML, or similar. Most of IDE will automatically apply syntax coloring and other goodies to inspect those files based on extension. Also, opening them in specific applications or passing them around to use as examples will be easier with the right extension.

## Dealing with Non-Deterministic output

This is not an exclusive problem of snapshot testing. Managing non-deterministic output is always a problem. In assertion testing, you can introduce property-based testing: instead of looking for exact values, you can look for desired properties of the output.

In snapshot testing, things are a bit more complicated. It is difficult to check the properties of a specific part of the output and ignore that specific value. Anyway, one solution is to look for patterns and do something with them: replacing them with a fixed but representative value or replacing them with a placeholder. Maybe it is possible to ignore that part of the output to compare with the snapshot.

In **Golden**, as it happens in other similar libraries, we can use `Scrubbers`. A Scrubber encapsulates a regexp match and replace, so you use the regexp to describe the fragment of the subject that should be replaced and provide a sensible substitution.

You can see an example right now. In the following test, the subject has non-deterministic content because it takes the current time when the test is executed, so it will be different on every run. We want to avoid the failure of the test by replacing the time data with something that never changes.

In this example, the `scrubber` matches any time formatted like "15:04:05.000" and replaces it with "&lt;Current Time&gt;".

```go
func TestShouldScrubData(t *testing.T) {
    scrubber := golden.NewScrubber("\\d{2}:\\d{2}:\\d{2}.\\d{3}", "<Current Time>")

    // Here we have a non-deterministic subject
    subject := fmt.Sprintf("Current time is: %s", time.Now().Format("15:04:05.000"))

    gld.Verify(t, subject, golden.WithScrubbers(scrubber))
}
```

You could use any replacement string. In the previous example, we used a placeholder. But you could prefer to use an arbitrary time so when inspecting the snapshot you can see realistic data. This can be useful if you are trying to get approval for the snapshot, as long as it avoids having to explain that the real thing will be showing real times or whatever non-deterministic data that the software generates.

### Replacing fields in Json Files with PathScrubbers

If you are testing Json files you probably will want to scrub specific fields in the output. Instead of searching for a pattern in the file, you want to search for a path to a field. We have you covered with PathScrubber.

The PathScrubber allows you to specify a path and replace its contents unconditionally. If the path is not found, no replacement will be performed.

```go
func TestBasicPathScrubbing(t *testing.T) {
    scrubber := golden.NewPathScrubber("object.other.remark", "<Replacement>")

    subject := `{"object":{"id":"12345", "name":"My Object", "count":1234, "validated": true, "other": {"remark": "accept"}}}`
    result := scrubber.Clean(subject)
    want := `{"object":{"id":"12345", "name":"My Object", "count":1234, "validated": true, "other": {"remark": "<Replacement>"}}}`
    assert.Equal(t, want, result)
}
```

### Caveats

Scrubbers are handy, but it is not advisable to use lots of them in the same test. Having to use a lot of scrubbers means that you have a lot of non-deterministic data in the output, so replacing it will make your test pretty useless because the data in the snapshot will be placeholders or replacements for the most part.

Review if you can avoid that situation by checking things like:

* Avoid the use of random test data. If you need some _Dummy_ objects, create them with fixed data. Learn about the Object Mother pattern to manage your test examples.
* Usually only times, dates, identifiers, and randomly generated things (like in a password generator), are non-deterministic. Scrubbing should be limited to them and only if they are generated _inside_ the Subject Under Test. For example, if your code under test creates a random identifier, introduce and scrubber. But if you have a password field that is passed to the code under test, set any arbitrary valid value.
* Consider software design: Avoid global state dependencies in the SUT, such as the system clock or random generators. Encapsulate that in objects or functions that you can inject in the SUT and double them in your tests, providing predictable outputs.

### Create Custom Scrubbers

If you find that you are using many times the same regexp and replacement, consider creating a custom _Scrubber_.

For example, in the previous test, we used:

```go
scrubber := golden.NewScrubber("\\d{2}:\\d{2}:\\d{2}.\\d{3}", "<Current Time>")
```

You can encapsulate it in a constructor function. This way you can easily reuse it in all the tests in which it makes sense.

```go
func TimeScrubber(opts ...ScrubberOption) Scrubber {
    return golden.NewScrubber(
       "\\d{2}:\\d{2}:\\d{2}.\\d{3}", 
       "<Current Time>", 
       opts...
    )
}

scrubber := TimeScrubber()
```

When writing Scrubbers, you should support the `opts ...ScrubberOption` parameter as in the previous example. This will allow your Scrubber to use `ScrubberOptions`, so you can modify the replacement or the context.

This will allow you to enforce policies to scrub snapshots, introducing Scrubbers that are useful for your domain needs.

### Predefined Scrubbers

`CreditCard`: obfuscates credit card numbers

```go
func TestCreditCard(t *testing.T) {
    scrubber := golden.CreditCard()
    subject := "Credit card: 1234-5678-9012-1234"
    assert.Equal(t, "Credit card: ****-****-****-1234", scrubber.Clean(subject))
}
```

`ULID`: replaces a Universally Unique Lexicographically Sortable Identifier

```go
t.Run("should replace ULID", func(t *testing.T) {
    scrubber := golden.ULID()
    subject := "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3"
    assert.Equal(t, "This is an ULID: <ULID>", scrubber.Clean(subject))
})
```


### Options for Scrubbers

`Replacement`: allows you to customize the replacement of any scrubber supporting options. Some scrubbers will put a placeholder as a replacement, but in some cases, you could prefer a different placeholder.

```go
t.Run("should replace ULID with custom replacement", func(t *testing.T) {
    scrubber := golden.ULID(golden.Replacement("[[Another thing]]"))
    subject := "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3"
    assert.Equal(t, "This is an ULID: [[Another thing]]", scrubber.Clean(subject))
})
```

A fixed example of the value will help to better understand the generated snapshot to non-technical people.

```go
t.Run("should replace ULID with custom replacement", func(t *testing.T) {
    scrubber := golden.ULID(golden.Replacement("01HNB9N6T6DEB1XN10C58DT1WE"))
    subject := "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3"
    assert.Equal(t, "This is an ULID: 01HNB9N6T6DEB1XN10C58DT1WE", scrubber.Clean(subject))
})
```

`Format`: allows you to pass a format string that provides some context for replacements, so they will be only applied in certain parts of the output. In the following example, we only want to apply the obfuscation to the _Credit card_ field, but not to other codes that could be similar.

```go
t.Run("should obfuscated only credit card number", func(t *testing.T) {
    scrubber := golden.CreditCard(golden.Format("Credit card: %s"))
    subject := "Credit card: 1234-5678-9012-1234, Another code: 4561-1234-4532-6543"
    assert.Equal(t, "Credit card: ****-****-****-1234, Another code: 4561-1234-4532-6543", scrubber.Clean(subject))
})
```

## How snapshots are named

By default, test names are used to auto-generate the snapshot file name.

This test:

```go
func TestSomething (t *testing.T) {
    //...
}
```

Will generate the snapshot: `__snapshots/TestSomething.snap`

Inner tests with `t.Run` will work this way. The Test:

```go
func TestSomething (t *testing.T) {
    t.Run("should do something", func(t *testing.T) {
       //...
    })
}
```

Will generate the snapshot: `__snapshots/TestSomething/should_do_something.snap`

You can customize the snapshot file name by passing the option `golden.Snapshot("new_snapshot_name")`. You must do this if you want to have two or more different snapshots in the same test. The first one could use the default name.

