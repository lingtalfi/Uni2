The universe
==================
2019-02-26



The universe is composed of planets organized in galaxies.



The smallest component: the planet, is a package of php classes using the @kw(BSR-0) naming convention.


Why using BSR-0?
----------------
The benefit of using the BSR-0 convention across the whole universe is simple: we only need one autoloader for the whole universe!

And so, once you start using the universe, it's very easy to add new planets, because they are handled by the autoloader automatically.


In an application, planets are usually stored in one directory called universe.


```txt
- /path/to/my_app/universe/
----- planetA/ 
----- planetB/ 
----- planetC/ 
----- ... 
``` 

To add a planet, just add another directory in the universe directory, and make sure all your classes inside this directory respect the BSR-0 naming convention.

As a result, you will be able to use your planet right away (i.e. your class will be handled by the autoloader automatically).


In the universe, the autoloader by default is the @object(BumbleBee autoloader).



What if I want to use a non BSR-0 code?
-----------------

You can.
The planet itself has to be BSR-0 compliant.

However, a planet can have its own dependencies, which can be any code, really.

So if you want to use the [tcpdf library](https://github.com/tecnickcom/tcpdf), or the [SwiftMailer component](https://swiftmailer.symfony.com/), you'll be able to do that, but from a (BSR-0) planet.




Dependencies and the galaxy identifier
-----------------------


Dependencies is the darkest topic in this document.

Let's try to clarify this concept right away.


A planet can have dependency to one or more other **packages**. 

A package (in the scope of this section) can be one of:

- a **planet**
- not a planet (like the tcpdf library, or the SwiftMailer component for instance)



Our goal being to resolve dependencies automatically via a tool like @concept(uni tool) for instance,
then the first questions that arise when trying to resolve a dependency are:

- where do I download it?
- How do I download it (do I need extra-steps after having downloading the package)?


And so the galaxy identifier is a big part of the answer to those questions.


The galaxy identifier acts as:

- a namespace for the planets, allowing us to organize the planets into logical units (usually, if an author creates one galaxy, she names it after her own name)
- an identifier for the uni tool (or alike) that will map to a download technique


The download technique defines a general scheme to download the package.


So for instance in the following **dependencies.byml** file (which is the file used by planets to express their dependencies):
 
 
```yaml
- dependencies:
    - ling:
        - Bat: *  
        - ArrayToString: 1.4.0  
    - git:
        - tecnickcom/tcpdf: *
- post_install:
    - blabla  
```


We can see in the dependencies section that we have two galaxy identifiers:

- ling
- git

Those correspond to two download techniques registered before hand in the **uni tool**,
and so when the uni tool reads that file, he knows that it will need to use the "ling downloading technique" to download the
Bat and the ArrayToString packages (planets in this case), and the "git downloading technique" to download the "tecnickcom/tcpdf" package.



Sometimes though, the downloading technique is not precise enough.
For instance, in the case of the SwiftMailer component which is not BSR-0 compliant, we might want to create a new autoloader. 

To solve all those kind of fine-tuning problems, we can use the directives of the "post_install" section (in the **dependencies.byml** file),
which is an extensible section designed to complete the installation.



