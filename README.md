# Advent of Code 2023

## Prerequisites

- PHP 8.2

## How to install

```bash
$ (symfony) composer install
```

## How to use

### Adding a new solver if none exists

- **`{day}`: single digits must be prefixed with a `0`, ie. `3` => `03` etc.**
- Resources:
    - add part one and part two of the statement in `src/Resources/doc/Day{day}.md`
    - write the test input in a `{day}.txt` in `src/Resources/input/test/` if it's the same test input for both parts,
      or as two separate files, appended with `_1` or `_2` to differentiate them
    - write your input in a `{day}.txt` in `src/Resources/input/`
- Add a new service in `src/Conundrum/`  (by copying model
  class `ExampleConundrumSolver.php`): `Day{day}ConundrumSolver` and make sure to extend `AbstractConundrumSolver`
    - Don't forget to pass the separator as second argument in the parent constructor call if it's different from
      `PHP_EOL`
- Implement your logic in both `partOne()` and `partTwo()` and have them return your result

### Displaying the results

Just run:

```bash
$ (symfony) php application.php app:resolve-conundrums 1
```

With the day's number as the only argument (both `1` and `01` are valid options).
You can use the option `-T/--with-test-input` if you want to test your logic on the test input.
