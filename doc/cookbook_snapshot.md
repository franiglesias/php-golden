# Testing existing code with a snapshot

In this recipe we will learn how to use **Golden** to introduce some basic tests for a piece of code that is unknown for us and has no tests. We will use snapshots to put the code under test and start understanding how it works.

## The problem

I want to put under test a piece of legacy code before refactoring it. I don't know how it works. The output is a bit complex because it is not a simple value. Instead, it is a text representation of a complex object.

## Preparation

Install the library if you have not done so before. I have to make a `composer update` first in this project to run it under php 8.3.

```shell
composer require --dev franiglesias/golden
```

## The example

For this example, I'm going to use the go version of Theatrical Play Kata, from Emily Bache. The exercise is about refactoring a piece of code that prints an invoice. The unit returns the printed statement as a plain text.

The actual exercise provides several tests, but we are going to work as if we don't have any test at all. So, delete all tests and leave untouched the `src` folder. This recipe will show you how to use **Golden** to generate a simple snapshot test that captures the output. This will be a pretty good starting point when we know nothing about the code.

This is the code we want to put under test.

```php
class StatementPrinter
{
    public function print(Invoice $invoice, array $plays): string
    {
        $totalAmount = 0;
        $volumeCredits = 0;

        $result = "Statement for {$invoice->customer}\n";
        $format = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        foreach ($invoice->performances as $performance) {
            $play = $plays[$performance->play_id];
            $thisAmount = 0;

            switch ($play->type) {
                case 'tragedy':
                    $thisAmount = 40000;
                    if ($performance->audience > 30) {
                        $thisAmount += 1000 * ($performance->audience - 30);
                    }
                    break;

                case 'comedy':
                    $thisAmount = 30000;
                    if ($performance->audience > 20) {
                        $thisAmount += 10000 + 500 * ($performance->audience - 20);
                    }
                    $thisAmount += 300 * $performance->audience;
                    break;

                default:
                    throw new Error("Unknown type: {$play->type}");
            }

            // add volume credits
            $volumeCredits += max($performance->audience - 30, 0);
            // add extra credit for every ten comedy attendees
            if ($play->type === 'comedy') {
                $volumeCredits += floor($performance->audience / 5);
            }

            // print line for this order
            $result .= "  {$play->name}: {$format->formatCurrency($thisAmount / 100, 'USD')} ";
            $result .= "({$performance->audience} seats)\n";

            $totalAmount += $thisAmount;
        }

        $result .= "Amount owed is {$format ->formatCurrency($totalAmount / 100, 'USD')}\n";
        $result .= "You earned {$volumeCredits} credits";
        return $result;
    }
}

```

And these are the data types:

```php
class Invoice
{
    /**
     * @var string
     */
    public $customer;

    /**
     * @var array
     */
    public $performances;

    public function __construct(string $customer, array $performances)
    {
        $this->customer = $customer;
        $this->performances = $performances;
    }
}

class Performance
{
    /**
     * @var string
     */
    public $play_id;

    /**
     * @var int
     */
    public $audience;

    /**
     * @var Play
     */
    public $play;

    public function __construct(string $play_id, int $audience)
    {
        $this->play_id = $play_id;
        $this->audience = $audience;
    }
}


class Play
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function __toString()
    {
        return (string) $this->name . ' : ' . $this->type;
    }
}
```

First, let's study the signature:

```php
class StatementPrinter
{
    public function print(Invoice $invoice, array $plays): string
    {
    }
}
```

We need to pass a populated `Invoice` object and an associative array of `Play`, which keys as simple strings. The method will produce a `string` with the output.

## First test

We are going to create the first test in the `tests` folder. I'm naming it `GoldenStatementPrinterTest.php`. At this point, I only want to be sure that I can set up a test that can run. We want to have something like this. No data yet:

```go
func TestBasicStatementPrinter(t *testing.T) {
	// Prepare data needed
	invoice := theatre.Invoice{}
	plays := make(map[string]theatre.Play)
	printer := theatre.StatementPrinter{}
	
	// Execute the method and obtain the result
	statement, err := printer.Print(invoice, plays)
	if err != nil {
		t.Fatalf("error: %s", err.Error())
	}
	
	// Verify creating an snapshot
	golden.Verify(t, statement, golden.Folder("testdata"))
}
```


```php
final class GoldenStatementPrinterTest extends TestCase
{
    use Golden;
    #[Test]
    /** @test */
    public function shouldPrintEmptyStatement(): void
    {
        $printer = new StatementPrinter();
        
        // Prepare data needed
        $invoice = new Invoice("Smith Ltd.", []);
        $plays = [];

        // Execute the method and obtain the result
        $statement = $printer->print($invoice, $plays);

        // Verify creating an snapshot
        $this->verify($statement);
    }
}
```

This test works. In fact, it captures the following output in the file `theatre/testdata/TestBasicStatementPrinter.snap`:

```
Statement for Smith Ltd.
Amount owed is $0.00
You earned 0 credits
```

That was great. The test executes the code without errors. It is not useful to print a statement without nothing to invoice, but it provides us with some knowledge about the code. 

Of course, we will need to pass some data. So, let's take a look at the structs Invoice, Play and Performance, again:

```php
class Invoice
{
    /**
     * @var string
     */
    public $customer;

    /**
     * @var array
     */
    public $performances;

    public function __construct(string $customer, array $performances)
    {
        $this->customer = $customer;
        $this->performances = $performances;
    }
}

class Performance
{
    /**
     * @var string
     */
    public $play_id;

    /**
     * @var int
     */
    public $audience;

    /**
     * @var Play
     */
    public $play;

    public function __construct(string $play_id, int $audience)
    {
        $this->play_id = $play_id;
        $this->audience = $audience;
    }
}


class Play
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function __toString()
    {
        return (string) $this->name . ' : ' . $this->type;
    }
}
```

Invoice takes a `Customer` string and a slice of `Performances`. Each `Performance` takes a `PlayID` and an `Audience`. A `Play` has a `Type` and a `Name`.

Now, let's look at code. `Customer` is a value that is printed directly in the statement. Not so much mystery here.

```php
$result = "Statement for {$invoice->customer}\n";
```

It seems that `Performance` holds a reference to a `Play`, but it sounds strange that the name of the property in `Performance` is `PlayID`, because there is no `Id` property in `Play`. Maybe the `PlayID` is its `Name`, maybe is simply an index. In fact, code says this: 

```php
$play = $plays[$performance->play_id];
```

So, `PlayID` could be anything, a number, or a name, provided that it acts as an identifier for a unique `Play`.

On the other hand, `Audience` is an `int` and it probably means the number of people who attended the `Performance`, so it should be important to compute the amount owed. We can several important values of `Audience` in the conditionals that decide different execution paths:

```php
if ($performance->audience > 30) {
    $thisAmount += 1000 * ($performance->audience - 30);
}
```

There is a mention in the previous snapshot to "credits", but we cannot see anything related in the data structures, so it is something computed by the code. Also, we can see that there are some important values for `Audience`. For example, audiences greater than 30 generate credits, and extra credits are added for every five (ten?) comedy attendees. This means that the `Type` of `Play` is important. 


```php
// add volume credits
$volumeCredits += max($performance->audience - 30, 0);
// add extra credit for every ten comedy attendees
if ($play->type === 'comedy') {
    $volumeCredits += floor($performance->audience / 5);
}
```

So, in order to create sample data for the test, we need to take into account:

* `Customer` is a string that has no influence on behavior.
* `Performance->audience` has two interesting value points: 20 and 30, depending on the `Type` of the `Play`, because those limits change the behavior when calculating amounts.
* `Type` of `Play` is very important, and we manage both "comedy" and "tragedy".

So, we will need:

|  type   | audience |
|:-------:|---------:|
| tragedy |       25 |
| tragedy |       30 |
| tragedy |       35 |
| comedy  |       15 |
| comedy  |       20 |
| comedy  |       25 |


We are designing a white box test, based upon a basic path strategy: we 
will decide the test conditions according to the critical values that we've found in the code.

Now, we create the input data based on the analysis we've just performed.

```php
#[Test]
/** @test */
public function shouldPrintCompleteStatement(): void
{
    $printer = new StatementPrinter();
    
    $plays = [
        "hamlet" => new Play("Hamlet", "tragedy"),
        "as-you-like" => new Play("As You Like", "comedy"),
    ];

    $performances = [
        new Performance("hamlet", 25),
        new Performance("hamlet", 30),
        new Performance("hamlet", 35),
        new Performance("as-you-like", 15),
        new Performance("as-you-like", 20),
        new Performance("as-you-like", 25),
    ];

    $invoice = new Invoice("Smith Ltd.", $performances);

    $statement = $printer->print($invoice, $plays);

    $this->verify($statement);
}
```

Let's run the test, and this is the result:

```
Statement for Smith Ltd.
  Hamlet: $400.00 (25 seats)
  Hamlet: $400.00 (30 seats)
  Hamlet: $450.00 (35 seats)
  As you like: $345.00 (15 seats)
  As you like: $360.00 (20 seats)
  As you like: $500.00 (25 seats)
Amount owed is $2,455.00
You earned 17 credits
```

The test provides 95.8% coverage. The only execution path that doesn't run is the case in which the Play Type is unknown. This looks as a pretty good test to start.

## Wrapping up

Using the snapshot test feature of **Golden** and some white box testing, we can put code under test with minimal effort. Of course, there are plenty of examples of situations in which setting up a test won't be as easy. But, if you are able to capture the output of the unit you want to test, take for granted that **Golden** would be a great tool.

## Where to go from here

Once you have a snapshot test in place that covers most of the code, you are ready to start refactoring the code, knowing that the test will show if you break the behavior.

Usually, a snapshot test like this will not remain for a long time in the project. Hopefully, the code will be refactored to smaller objects with well-defined responsibilities that will be properly unit tested. In some situations, the snapshot test could make a good acceptance test, but if you are going to introduce new business rules you will need to get rid of it.

In that situation we recommend to try Approval mode.
