The uni tool
==================
2019-02-26


The **uni tool** is a command line tool used to handled planet dependencies.




Technically speaking, it's a @object(console application) with the following commands:





- list-planet: this command will list the planets in the current application, optionally with their version number.




The commands
================



The list-planet command
-------------------------

this command will list the planets in the current application, optionally with their version number.


```bash
uni list-planet [-v]
```

Options:

- v: will display the version number along with the planet name





Planet constraints
=================

In order to be compliant with the uni-tool, a planet needs to have the following files at its root:


- dependencies.byml: @concept(the dependency file)
- meta-info.byml: @concept(the meta-info file)



Also, the planets must be uploaded on github.com, and each version of the planet must be represented by a tag
with the version number (as the tag name).




Dependency system
=================

The uni tool cares only about planets.

So the import command basically will always receive a planet as the parameter:

```bash
uni import Bat
```

However, planets can depend from whatever they use, like for instance the [tcpdf library](https://github.com/tecnickcom/TCPDF),
or the [SwiftMailer component](https://swiftmailer.symfony.com/) from another library.

The planet is almost on its own to cope with those maverick dependencies.

This means that the uni-tool will not care about the planet dependencies if they are something else than a planet,
and so the uni-tool will not try to check the version number or import dependencies of such non-planet packages.


However, the uni-tool provides some help even for those cases:

- in the dependencies section of the **dependencies.byml** file, the universe accepts any dependency system (i.e. not only galaxy identifiers). 
    The perhaps most popular non-galaxy dependency system is git, which let the planet indicates its dependency to a git hosted repo.
    When the uni-tool sees such a non-conform dependency, it will use the download technique associated with the dependency system.
    In the case of the git dependency system, it will download/clone the repository under the **universe-dependencies/git** directory.
    The **universe-dependencies** is a directory next to the **universe** directory of your application.  

- the post_install section is dedicated to handling all kinds of problems for such non-conforming dependencies.
    Planets can use the post_install directives to "manually" install their dependencies when they are imported by the uni-tool.
    
    
     

To create your own galaxy
==========================

Start by creating a **universe** directory on your local machine.

The name is **universe** because your galaxy will be merged with the existing universe.

There is only one universe for all. (i.e. the concept of multi-verse is dropped). 


Then, create a planet inside your universe directory.
Be sure to respect the steps in the @concept(planet creation document).

TODO:
- step1: BSR0
- step2: meta-info
- step3: dependencies if any
- step4: version number: comparison friendly
- step5: when a new version of the planet is published, the dependency master file should be updated as well. 

 


Then, create a github.com account, the name of the account should represent your galaxy name.


Finally, you need to register your galaxy to the uni-tool.

Simply contact me for now. If I ever have too many requests, I'll probably opt for a more automated system,
but for now this is not the case, so a simple email request will do.







  







Meta-info
=============


The **meta-info** gives information about the planet.

It has the form of a **meta-info.byml** [babyYaml](https://github.com/lingtalfi/BabyYaml) file
and should be at the root of any planet. 


It can contain any number of information that the planet author wants.
However the "required" entries are the following:


- version: the version number of the planet
- galaxy: the name of the galaxy the planet comes from



Example
------------

```yaml
- version: 1.4.0
- galaxy: line
```








