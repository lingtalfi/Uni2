[Back to the Ling/Uni2 api](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2.md)



The OutputHelper class
================
2019-03-12 --> 2019-03-12






Introduction
============

The OutputHelper class.
Contains helpers related to the [output object](https://github.com/lingtalfi/CliTools/blob/master/doc/api/Ling/CliTools/Output/OutputInterface.md).



Class synopsis
==============


class <span class="pl-k">OutputHelper</span>  {

- Methods
    - public static [success](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/success.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [info](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/info.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [command](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/command.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [warning](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/warning.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [error](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/error.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [discover](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/discover.md)(?$message, Ling\CliTools\Output\OutputInterface $output, int $indent = 0) : void
    - public static [i](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/i.md)($level = 0) : string
    - public static [j](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/j.md)($level = 0) : string
    - public static [s](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/s.md)($level = 0) : string

}






Methods
==============

- [OutputHelper::success](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/success.md) &ndash; Writes a success message to the output.
- [OutputHelper::info](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/info.md) &ndash; Writes an info message to the output.
- [OutputHelper::command](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/command.md) &ndash; Writes a command message to the output.
- [OutputHelper::warning](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/warning.md) &ndash; Writes a warning message to the output.
- [OutputHelper::error](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/error.md) &ndash; Writes an error message to the output.
- [OutputHelper::discover](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/discover.md) &ndash; Writes a discover message to the output.
- [OutputHelper::i](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/i.md) &ndash; Returns an indent string which $length is proportional to the given $level.
- [OutputHelper::j](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/j.md) &ndash; Returns another indent string which $length is proportional to the given $level.
- [OutputHelper::s](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/OutputHelper/s.md) &ndash; Returns an indent block of white space, which $length is proportional to the given $level.





Location
=============
Ling\Uni2\Helper\OutputHelper


SeeAlso
==============
Previous class: [DependencyMasterHelper](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/Helper/DependencyMasterHelper.md)<br>Next class: [LocalServer](https://github.com/lingtalfi/Uni2/blob/master/doc/api/Ling/Uni2/LocalServer/LocalServer.md)<br>
