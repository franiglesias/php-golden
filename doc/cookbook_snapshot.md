# Testing existing code with a snapshot

In this recipe we will learn how to use **Golden** to introduce some basic tests for a piece of code that is unknown for us and has no tests. We will use snapshots to put the code under test and start understanding how it works.

## The problem

I want to put under test a piece of legacy code before refactoring it. I don't know how it works. The output is a bit complex because it is not a simple value. Instead, it is a text representation of a complex object.

## Preparation

Install the library if you have not done so before.

```shell
composer require --dev franiglesias/golden
```

## The example

For this example, I'm going to use the go version of Theatrical Play Kata, from Emily Bache. The exercise is about refactoring a piece of code that prints an invoice. The unit returns the printed statement as a plain text.

The actual exercise provides several tests, but we are going to work as if we don't have any test at all. So, delete all tests leaving only the `StatementPrinter.go` and `types.go` files. This recipe will show you how to use **Golden** to generate a simple snapshot test that captures the output. This will be a pretty good starting point when we know nothing about the code.

This is the code we want to put under test.

```go
type StatementPrinter struct{}

func (StatementPrinter) Print(invoice Invoice, plays map[string]Play) (string, error) {
	totalAmount := 0
	volumeCredits := 0
	result := fmt.Sprintf("Statement for %s\n", invoice.Customer)

	ac := accounting.Accounting{Symbol: "$", Precision: 2}

	for _, perf := range invoice.Performances {
		play := plays[perf.PlayID]
		thisAmount := 0

		switch play.Type {
		case "tragedy":
			thisAmount = 40000
			if perf.Audience > 30 {
				thisAmount += 1000 * (perf.Audience - 30)
			}
		case "comedy":
			thisAmount = 30000
			if perf.Audience > 20 {
				thisAmount += 10000 + 500*(perf.Audience-20)
			}
			thisAmount += 300 * perf.Audience
		default:
			return "", fmt.Errorf("unknown type: %s", play.Type)
		}

		// add volume credits
		volumeCredits += int(math.Max(float64(perf.Audience)-30, 0))
		// add extra credit for every ten comedy attendees
		if play.Type == "comedy" {
			volumeCredits += int(math.Floor(float64(perf.Audience) / 5))
		}

		// print line for this order
		result += fmt.Sprintf("  %s: %s (%d seats)\n", play.Name, ac.FormatMoney(float64(thisAmount)/100), perf.Audience)
		totalAmount += thisAmount
	}
	result += fmt.Sprintf("Amount owed is %s\n", ac.FormatMoney(float64(totalAmount)/100))
	result += fmt.Sprintf("You earned %d credits\n", volumeCredits)
	return result, nil
}
```

And these are the data types:

```go
type Performance struct {
	PlayID   string
	Audience int
}

type Play struct {
	Name string
	Type string
}

type Invoice struct {
	Customer     string
	Performances []Performance
}
```

First, let's study the signature:

```go
func (StatementPrinter) Print(invoice Invoice, plays map[string]Play) (string, error) 
```

We need to pass a populated `Invoice` struct and a map of `Play`, which keys as simple strings. The method will produce a `string` with the output and an `error`.

## First test

We are going to create the first test in the `theatre` package. I'm naming it `golden_statement_printer_test.go`. At this point, I only want to be sure that I can set up a test that can run.

```go
package theatre_test

import "testing"

func TestBasicStatementPrinter(t *testing.T) {
	
}
```

We want to have something like this. No data yet:

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

This test works. In fact, it captures the following output in the file `theatre/testdata/TestBasicStatementPrinter.snap`:

```
Statement for 
Amount owed is $0.00
You earned 0 credits
```

That was great. The test executes the code without errors. It is not useful to print a statement without nothing to invoice, but it provides us with some knowledge about the code. 

Of course, we will need to pass some data. So, let's take a look at the structs Invoice, Play and Performance, again:

```go
type Performance struct {
	PlayID   string
	Audience int
}

type Play struct {
	Name string
	Type string
}

type Invoice struct {
	Customer     string
	Performances []Performance
}
```

Invoice takes a `Customer` string and a slice of `Performances`. Each `Performance` takes a `PlayID` and an `Audience`. A `Play` has a `Type` and a `Name`.

Now, let's look at code. `Customer` is a value that is printed directly in the statement. Not so much mystery here.

```go
result := fmt.Sprintf("Statement for %s\n", invoice.Customer)
```

It seems that `Performance` holds a reference to a `Play`, but it sounds strange that the name of the property in `Performance` is `PlayID`, because there is no `Id` property in `Play`. Maybe the `PlayID` is its `Name`, maybe is simply an index. In fact, code says this: 

```go
    play := plays[perf.PlayID]
```

So, `PlayID` could be anything, a number, or a name, provided that it acts as an identifier for a unique `Play`.

On the other hand, `Audience` is an `int` and it probably means the number of people who attended the `Performance`, so it should be important to compute the amount owed. We can several important values of `Audience` in the conditionals that decide different execution paths:

```go
if perf.Audience > 30 {
    thisAmount += 1000 * (perf.Audience - 30)
}
```

There is a mention in the previous snapshot to "credits", but we cannot see anything related in the data structures, so it is something computed by the code. Also, we can see that there are some important values for `Audience`. For example, audiences greater than 30 generate credits, and extra credits are added for every five (ten?) comedy attendees. This means that the `Type` of `Play` is important. 

```go
// add volume credits
volumeCredits += int(math.Max(float64(perf.Audience)-30, 0))
// add extra credit for every ten comedy attendees
if play.Type == "comedy" {
    volumeCredits += int(math.Floor(float64(perf.Audience) / 5))
}
```

So, in order to create sample data for the test, we need to take into account:

* `Customer` is a string that has no influence on behavior.
* `Performance.Audience` has two interesting value points: 20 and 30, depending on the `Type` of the `Play`, because those limits change the behavior when calculating amounts.
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

```go
func TestPrintStatementForInvoice(t *testing.T) {
	plays := make(map[string]theatre.Play)

	plays = map[string]theatre.Play{
		"hamlet": {
			Name: "Hamlet",
			Type: "tragedy",
		},
		"as-you-like": {
			Name: "As You Like",
			Type: "comedy",
		},
	}
	
	invoice := theatre.Invoice{
		Customer: "Smith Ltd.",
		Performances: []theatre.Performance{
			{
				PlayID:   "hamlet",
				Audience: 25,
			},
			{
				PlayID:   "hamlet",
				Audience: 30,
			},
			{
				PlayID:   "hamlet",
				Audience: 35,
			},
			{
				PlayID:   "as-you-like",
				Audience: 15,
			},
			{
				PlayID:   "as-you-like",
				Audience: 25,
			},
			{
				PlayID:   "as-you-like",
				Audience: 25,
			},
		},
	}
	
	printer := theatre.StatementPrinter{}

	statement, err := printer.Print(invoice, plays)
	if err != nil {
		t.Fatalf("error: %s", err.Error())
	}
	golden.Verify(t, statement, golden.Folder("testdata"))
}
```

Let's run the test, and this is the result:

```
Statement for Smith Ltd.
  Hamlet: $400.00 (25 seats)
  Hamlet: $400.00 (30 seats)
  Hamlet: $450.00 (35 seats)
  As You Like: $345.00 (15 seats)
  As You Like: $500.00 (25 seats)
  As You Like: $500.00 (25 seats)
Amount owed is $2,595.00
You earned 18 credits
```

The test provides 95.8% coverage. The only execution path that doesn't run is the case in which the Play Type is unknown. This looks as a pretty good test to start.

## Wrapping up

Using the snapshot test feature of **Golden** and some white box testing, we can put code under test with minimal effort. Of course, there are plenty of examples of situations in which setting up a test won't be as easy. But, if you are able to capture the output of the unit you want to test, take for granted that **Golden** would be a great tool.

## Where to go from here

Once you have a snapshot test in place that covers most of the code, you are ready to start refactoring the code, knowing that the test will show if you break the behavior.

Usually, a snapshot test like this will not remain for a long time in the project. Hopefully, the code will be refactored to smaller objects with well-defined responsibilities that will be properly unit tested. In some situations, the snapshot test could make a good acceptance test, but if you are going to introduce new business rules you will need to get rid of it.

In that situation we recommend to try Approval mode.
