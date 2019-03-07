Uni-tool configuration
=========================
2019-02-27



The uni-tool has its own configuration.



The configuration is actually stored as a @page(babyYaml) file on the computer.
It's location is under the Uni2 planet directory:

- private/configuration/conf.byml


It's exact location can be displayed with the "confpath" command.


So the configuration can be represented as an array with the following entries:
(all booleans are either 0 or 1)


```yaml
local_server:
     root_dir: # string = null. The location of the local server root dir.
     is_active: # int = 1 (0|1). whether to use the local server.
     use_auto_cache: # int = 1 (0|1). whether to use the local server auto cache system.

automatic_updates:
     is_active: #  int = 1 (0|1). Whether to use automatic updates.
     frequency: #  int = 5. The number of days to wait before checking for new updates.
     mode: #  string = auto (quiet|note|prompt|auto). Defines what to do when new updates are available.
           # - quiet: do nothing
           # - note: will simply display the text: "New updates are available. Use the upgrade command to upgrade,
           #         or the upgrade-diff command to see what updates are available."
           #
           #         This text will be displayed until the upgrade command is called.
           #
           # - prompt: will show the user the new updates available (result of the upgrade-diff command),
           #         and ask her if she wants to perform the upgrade.
           #
           #         This question will be prompted until the upgrade command is called.
           # - auto: will upgrade automatically, and display an "upgrade diff text" for every planet.
           #



```



The local server
----------------
The local server acts as a proxy of the web: it helps importing the planet dependencies much faster,
by simply copying them from the local server on your machine, thus saving all sorts of http requests.

To prepare a local server, the fastest way is to download the whole @concept(galaxies) on your local computer
(this also works with any @concept(download technique) in general).

Actually, there is a well-defined structure that you need to implement:
the name of the directory containing your local server is referred to as the local server's root dir.
The root dir direct children must be directories which name are the galaxy identifiers (or download technique name).

So for instance if you use the ling galaxy identifier and the git download technique, your local server tree
would look like this:

```txt
- local_server_root_dir/
----- ling/
--------- planetA
--------- planetB
--------- ...
----- git/
--------- repoA              (just the name after the "https://github.com/" prefix)
--------- repoB/MyTool
--------- repoC
--------- ...
```

If a dependency is not found in your local server, the uni tool will fallback to the traditional
http request technique.

You can de-activate the local server completely by setting the local_server.is_active configuration directive to 0.

Another way to build the local server is to let him build itself naturally, as you download dependencies.
In fact, every time you download a dependency (using the import command on a planet which has dependencies),
the local server stores this dependency in the appropriate location for later use, so that you don't need to worry
about updating your local server manually.
This technique is called auto caching and is activated by default.
You can de-activate auto-caching by setting the local_server.use_auto_cache directive to 0.

Note: auto_caching works well with the automatic_updates system too (see the automatic_updates directive).




Automatic updates
----------------

This is a system where every x days (x being the value of the automatic_updates.frequency directive), the uni-tool checks
for updates for every @concept(dependency system) that it knows of (and by design it knows them all).
