The universe
==================
2019-03-06



The universe is composed of planets organized in galaxies.



The smallest component: the planet, is a php package (a directory containing php classes) using the @page(BSR-1) naming convention.


Why using BSR-1?
----------------
The benefit of using the **BSR-1** convention across the whole universe is simple: we only need one autoloader for the whole universe!

And so, once you start using the universe, it's very easy to add new planets, because they are handled automatically by the autoloader.


In an application, planets are usually stored in a directory called **universe**.
Planets are organized in galaxies:


```txt
- /path/to/my_app/universe/
----- galaxyOne/ 
--------- planetA/
--------- planetB/ 
--------- planetC/ 
--------- ... 
----- galaxyTwo/ 
--------- planetA/ 
--------- planetD/ 
--------- planetX309/ 
--------- Gargoyle/ 
--------- ... 
``` 




To add a planet, just add another directory in the universe directory, and make sure all your classes inside this directory respect the BSR-1 naming convention.

As a result, you will be able to use your planet right away (i.e. your class will be handled by the autoloader automatically).


In the universe, the autoloader by default is the @object(BumbleBee autoloader).



What if I want to use a non BSR-1 code?
-----------------

You can.
The planet itself has to be **BSR-1** compliant.

However, a planet can have its own dependencies, which can be any code really, including non **BSR-1** compliant packages.

So if you want to use the [tcpdf library](https://github.com/tecnickcom/tcpdf), or the [SwiftMailer component](https://swiftmailer.symfony.com/), you'll be able to do that, but as dependencies of a (BSR-1) planet.




The planet structure
====================


Meta info
-----------

TODO


Dependencies 
-----------------------


Dependencies is the darkest topic in this document.

Let's try to clarify this concept right away.


A planet can have dependencies to one or more other **packages**. 

A package (in the scope of this section) can be either:

- a **planet**
- not a planet (like the tcpdf library, or the SwiftMailer component for instance)


A planet expresses its dependencies to other packages via a file named **dependencies.byml**,
at the root of the planet directory.


The **dependencies.byml** file looks like this:
 
 
```yaml
- dependencies:
    - Ling:
        - Bat  
        - ArrayToString  
    - git:
        - https://github.com/tecnickcom/TCPDF/
- post_install: []  
```


As we can see, the **dependencies.byml** file is composed of two sections:

- dependencies
- post_install


The **dependencies** section indicates the packages the planet depends on.
It's an array of **dependency system** => **packages** (aka packageImportNames).

So for instance in the above example, we have two dependency systems:

- Ling 
- git


The **Ling** dependency system contains 2 packages:

- Bat
- ArrayToString

while the **git** dependency system contains only 1 package:
 
- https://github.com/tecnickcom/TCPDF/


A **dependency system** is basically an identifier representing a download technique.

So in the above example, the packages **Bat** and **ArrayToString** will be downloaded using the **Ling** download technique,
whereas the **https://github.com/tecnickcom/TCPDF/** package will be downloaded using the **git** download technique. 



There are two types of **dependency systems**:

- dependency systems for planets
- dependency systems for non-planets


There is one rule to know: the dependency systems for planets start with an uppercase letter, 
whereas the dependency systems for non-planets start with a lowercase letter.

The name of a dependency systems for planets is the name of the **galaxy** containing those planets.

All galaxies use the same default download technique (based on a github.com repository), which can be overridden on a per-galaxy basis. 
 



Sometimes though, the downloading technique is not precise enough to install a package.
For instance, in the case of the SwiftMailer component which is not **BSR-1** compliant, we might want to create a new autoloader. 

To solve all those kind of fine-tuning problems, we can use the directives of the "post_install" section of the **dependencies.byml** file,
which is an extensible section designed to complete the installation.



Nomenclature
----------


### long planet name
### planetId


### planet name

The planet name is a generic term which refers either to the [long planet name](#long-planet-name) and/or the [short planet name](#short-planet-name), depending on the context.

### short planet name

Todo:
Ling.Bat
