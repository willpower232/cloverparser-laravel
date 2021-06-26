# cloverparser-laravel

[![run-tests](https://github.com/willpower232/cloverparser-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/willpower232/cloverparser-laravel/actions/workflows/run-tests.yml)
![Coverage](https://laravel-coverage.s3.eu-west-2.amazonaws.com/willpower232/cloverparser-laravel/main.svg)

This is a wrapper which takes the percentage coverage generated by [willpower232/cloverparser](https://github.com/willpower232/cloverparser) and then gives you the ability to use Laravel to create a coverage badge SVG using a view and store that image somewhere using the storage class.

This is also compatible with Lumen but I haven't figured out how to statically analyse both Lumen and Laravel.

---

A former casual user of [codecov](https://codecov.io) and interested in controlling my own data, I decided to see how complicated it would be to operate a similar setup myself after [their uploader script was compromised](https://www.theregister.com/2021/04/19/codecov_warns_of_stolen_credentials/).

This is the second part of this project, turning the coverage percentage into something displayable and storing that somewhere easily accessible.

---

## Installation

```
composer require willpower232/cloverparser-laravel
```

## Usage

Set a path to your CloverParser instance and then you can store files. Paths are made up of a project folder and a branch as the base name of the file.

Storing files in a folder per project and with the branch name as the file name should allow the files to be structured in a way that makes sense and also in a way that allows for future use without having to look anything else up. As a branch continues and is tested more often, stale files are replaced so that only the most recent are stored.

If you are looking to create a dataset of coverage over time, you can record the percentages as you wish in your application (or even prefix the clover file names to avoid overwriting them).

```php
use WillPower232\CloverParserLaravel\CloverParser;

$urlToSVG = app(CloverParser::class)
	->setPath($project, $branch)
	->addFile($pathToCloverFile)
	->storeImage();
```

You could also store other files for your future reference.

```php
use WillPower232\CloverParserLaravel\CloverParser;

$parser = app(CloverParser::class)
	->setPath($project, $branch)
	->addFile($pathToCloverFile);

$urlToSVG = $parser->storeImage();

$urlToFile = $parser->store("$branch.clover", file_get_contents($pathToCloverFile));
```

Alternatively, making more use of Laravel

```php
$file = new \Illuminate\Http\File($pathToCloverFile);

$parser = app(CloverParser::class)
	->setPath($project, $branch)
	->addFile($file);

$urlToFile = $parser->store("$branch.clover", $file);
```

or via upload directly

```php
$file = $request->file('file');

$parser = app(CloverParser::class)
	->setPath($project, $branch)
	->addFile($file);

$urlToFile = $parser->store("$branch.clover", $file);
```

Don't forget to validate user uploaded files before hosting them yourself.

## Configuration

If you want to store the files in a different disk, you can specify its name in your environment.

```env
CLOVER_PARSER_DISK=s3
```