Uni-tool upgrade system
=========================
2019-02-27



In this document, I'll discuss the uni tool upgrade system.

I'll discuss the design and how I came up with it.



Summary
=========

- [The working context](#the-working-context)
- [The upgrade process](#the-upgrade-process)
- [The difference between the import and the reimport command](#the-difference-between-the-import-and-the-reimport-command)
- [The dependency master file](#the-dependency-master-file)



The working context
--------------------

So, we want to implement an upgrade command, but what's our working context:

we have a user with a local machine, and the @page(uni-tool repository) on the web.

On the user local machine, we have a local copy of the uni-tool, some applications,
and possibly a @concept(local server) (if the user uses one).


Here is what we have so far visually:

```txt

- WEB
----- uni-tool planet
 
- USER LOCAL MACHINE
----- uni-tool planet 
----- application A 
----- application B 
----- local server 
```

And so my first idea is to introduce the concept of dependency master:
a file that contains the dependencies for all the universe.


The dependency master file will be part of the uni-tool planet, and so our schema will look like this:


```txt

- WEB
----- uni-tool planet
--------- dependency-master.byml
 
- USER LOCAL MACHINE
----- uni-tool planet 
--------- dependency-master.byml
----- application A 
----- application B 
----- local server 
```



Now how is the dependency master file generated is outside the scope of this discussion, but suffices to say that 
armed with the dependency master file, the uni tool can resolve all dependencies for any planet.
See the details of the dependency master file structure in [the dependency master file](#the-dependency-master-file) section.


The upgrade process
-----------------------

However, at some point, planets on the web will evolve and their version number will increment.

Those changes will not be reflected immediately on the user local machine.

And so the goal of the upgrade system is to upgrade the user local machine so that it uses the most recent planets on the web.


So we will introduce an upgrade command, which will do the following:


- update the dependency master file if necessary (i.e. if the version number of the uni-tool on the web is greater than the version number on the uni-tool in the local machine)
- if there was an update, then upgrade all planets from the local server 
- finally, updates all the planets of the current application by internally calling the reimport-all command


Also, a couple of rules which must be implemented by the user, and which allows me to keep it simple:

- the user should never touch/modify/update the local server manually, only the uni-tool is allowed to interact with the local server
- the local server is always in sync with the local dependency master file, which means it's always true that the local dependency master file describes
        the local server state perfectly, and so we can reason about upgrade problems using only the dependency master file (i.e. we don't check the local server
        files). That's why the upgrade command updates the local server automatically every time.  




Now you might wonder what's the difference between the import command and the reimport command (and that would be fair since I just made up the reimport concept while writing this sentence).


The difference between the import and the reimport command
--------------------

The import command will import a planet from either the local server (if the local server is active and contains the planet), or from the web otherwise (i.e. if the local server
does not contain the planet).

However, the local dependency master map will be used (i.e. not the dependency master from the web). 
That's why, the user might want to launch the upgrade command first.



Also, if the planet dependency has some post_install directives (see @page(the new universe dependency system) document for more details), it will execute them as well.

Now before it starts the import process, the import command checks whether the planet already exists in the application.

If it does, the import command will not import the planet.

In other words, once you have a planet in your application, the import command will not reimport this planet.


However, you've guessed it, the reimport command will.

Actually, the reimport command is a little bit more sophisticated than that.

The reimport command has two operation modes:


- the default operation mode is to reimport the planet ONLY IF there is a newer version of it
- the second operation mode is to reimport the planet no matter what, and this is done by setting the -f flag (f for force) 



The dependency master file
----------------

The dependency master file contains all the dependencies of the universe.

Its structure looks like this:

```yaml
galaxies:
    ling:
        Bat:
            version: 1.0.0
            dependencies:
                - ling.ArrayToStringTool   
                - ling.CopyDir   
                - ling.Tiphaine   
                - ling.BeeFramework
            post_install: []
                         
```



So this file contains only one galaxies section containing all the dependencies.
That's because the uni-tool only cares about planets, and all planets are part of a galaxy.

The "galaxies" section is an array which keys are the names of the galaxies (in the above example, ling
is the name of the galaxy which is currently the only known galaxy as I'm writing those lines). 

Then each galaxy (i.e. ling) is an array containing all the **dependencyItems** representing meta information about the planets.
Notice that because of that design, it's not possible to register a planet without knowing the galaxy whence it comes from.


Each **dependencyItem** is an array with the following entries:

- version
- dependencies
- post_install






The **version** holds a string: the version number.
With this new version of the uni-tool, it is expected that all planets use a version number system
that is comparison friendly.

A system like [Semantic versioning](https://semver.org/) fits this description perfectly.
But other systems, such as the one used by Bat (just two numbers separated by a dot), or even an alphabetical but (incremental) system
such as A, B, C, ... can work.

The uni-tool will use the php less than operator (<) to decide whether or not a planet needs an update.






The **dependencies** section is an array of all package dependencies.

The format to indicate a package dependency is the following:

- package dependency: <dependencySystemIdentifier> <.> <packageName>

So, in the case of a galaxy like ling, the dependency system identifier is also the galaxy identifier (i.e. ling),
then the package name, in the case of a galaxy, is the planet name.

However, the dependency system identifier can be something else if the planet depends on something else.

An example of a non-galactic dependency system identifier is "git", which contains any github.com php repo which is 
not a planet. In this case, the name of the package is the relative url path to the repository: the string 
after the "https://github.com/" in the url.

Now each dependency system will have its own way of naming a package.

The list of the dependency systems details should be in @page(the dependency systems of the universe) document.

The list of the known galaxies can be found in @page(the known galaxies list) document.

  













 




