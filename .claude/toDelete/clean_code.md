# Clean Code Guidelines
Summary of "Clean Code" by Robert C. Martin

A summary of the main ideas from the "Clean Code: A Handbook of Agile Software Craftsmanship" book by Robert C. Martin (aka. Uncle Bob).

Code is clean if it can be understood easily – by everyone on the team. Clean code can be read and enhanced by a developer other than its original author. With understandability comes readability, changeability, extensibility and maintainability.
General rules

    Follow standard conventions.
    Keep it simple stupid. Simpler is always better. Reduce complexity as much as possible.
    Boy scout rule. Leave the campground cleaner than you found it.
    Always find root cause. Always look for the root cause of a problem.
    Follow the Principle of Least Surprise.
    Don't repeat yourself (DRY).
    Do not override safeties.

Design rules

    Keep configurable data (e.g.: constants) at high levels. They should be easy to change.
    Prefer polymorphism to if/else or switch/case.
    Separate multi-threading code.
    Prevent over-configurability.
    Use dependency injection.
    Follow Law of Demeter. A class should know only its direct dependencies.

Understandability tips

    Be consistent. If you do something a certain way, do all similar things in the same way.
    Use explanatory variables.
    Encapsulate boundary conditions. Boundary conditions are hard to keep track of. Put the processing for them in one place.
    Prefer dedicated value objects to primitive type.
    Avoid logical dependency. Don't write methods which works correctly depending on something else in the same class.
    Avoid negative conditionals.

Names rules

    Choose descriptive and unambiguous names.
    Make meaningful distinction.
    Use pronounceable names.
    Use searchable names.
    Replace magic numbers with named constants.
    Avoid encodings. Don't append prefixes or type information.

Functions rules

    Small.
    Do one thing and they should do it well.
    Use descriptive names.
    Prefer fewer arguments. Maximum 2 parameters. When more data is needed, use an object or collection to group related parameters.
    Have no side effects.
    Don't use flag arguments. Split method into several independent methods that can be called from the client without the flag.

Comments rules

    Always try to explain yourself in code. If it's not possible, take your time to write a good comment.
    Don't be redundant (e.g.: i++; // increment i).
    Don't add obvious noise.
    Don't use closing brace comments (e.g.: } // end of function).
    Don't comment out code. Just remove.
    Use as explanation of intent.
    Use as clarification of code.
    Use as warning of consequences.

Source code structure

    Newspaper Metaphor. Code should read like a newspaper article - headline (class name) at top, important summary (public methods) first, then details (private methods) below.
    Step-Down Rule. Functions should read top-to-bottom, each function leading to the next at a lower level of abstraction.
    Separate concepts vertically.
    Related code should appear vertically dense.
    Declare variables close to their usage.
    Dependent functions should be close.
    Similar functions should be close.
    Place functions in the downward direction.
    Keep lines short.
    Don't use horizontal alignment.
    Use white space to associate related things and disassociate weakly related.
    Don't break indentation.

Objects and data structures

    Hide internal structure.
    Understand the distinction: Objects hide data and expose behavior. Data structures expose data and have no behavior. Choose appropriately based on use case.
    Avoid hybrids structures (half object and half data).
    Should be small.
    Do one thing (Single Responsibility Principle).
    Small number of instance variables. If your class have too many instance variable, then it is probably doing more than one thing.
    Base class should know nothing about their derivatives.
    Better to have many functions than to pass some code into a function to select a behavior.
    Prefer non-static methods to static methods.

Tests

    One concept per test. Each test should verify a single concept or behavior.
    Fast.
    Independent.
    Repeatable.
    Self-validating.
    Timely.
    Readable.
    Easy to run.
    Use a coverage tool.

Code smells

    Rigidity. The software is difficult to change. A small change causes a cascade of subsequent changes.
    Fragility. The software breaks in many places due to a single change.
    Immobility. You cannot reuse parts of the code in other projects because of involved risks and high effort.
    Needless Complexity.
    Needless Repetition.
    Opacity. The code is hard to understand.

Error handling

    Don't mix error handling and code.
    Use Exceptions instead of returning error codes.
    Don't return null, don't pass null either.
    Throw exceptions with context.

