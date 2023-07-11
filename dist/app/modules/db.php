<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: DB ACCESS CONTROLLER (EXAMPLE)
 *
 * This file is an empty example file. Read the implementation below for more details.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2023.
 * @category   Database Accessa Module (example)
 * @version    1.0.0-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 *
 *
 */

?>

<!--
--------------------------------------------------------------------------------------
This has been PURPOSEFULLY ADDED AT THE END OF THE PHP TAG as a HTML HARMLESS COMMENT.
--------------------------------------------------------------------------------------

This file is not loaded or required by any other file. It is just an EXAMPLE of how
you can use THIS FOLDER to create a file (or files) and easily build collection of
functions (modules) to support your application needs while keeping things organized.

Modify this page as needed to support your database access and handling requirements.

This specific example is for a DATABASE MODULE, but this folder (/modules/) is here so
you can easily expand your application any way you need. It is also an example of how
the Boilerplate is a ready to go structure for the developers who prefer to write their
code in the Model-view-controller (MVC) mode - despite we not necessarily enforcing it!

In this example, the idea is to have use your settings in conjunction with the provided
Database Modules/Helpers (PDO) and the CONFIG FILE.

Notice the current example assumes you have a 'db.json' file in your /.config/ folder
similar to this one:

    {
        "database": {
            "db_A": {
                "server"  : "localhost",
                "database": "database_name",
                "username": "database_username",
                "password": "database_password"
            },
            "db_B": {
                "server"  : "anotherhost",
                "database": "database_anothername",
                "username": "database_anotherusername",
                "password": "database_anotherpassword"
            }
        }
    }

All .json files the /.config/ folder are loaded automatically during the Boilerplate initialization,
and it will populate the "$env" Array with the equivalent of the following structure:

    $env['database']['db_A']['server']   = 'localhost';
    $env['database']['db_A']['database'] = 'database_name';
    $env['database']['db_A']['username'] = 'database_username';
    $env['database']['db_A']['password'] = 'database_password';

Based on that, you can load your database settings and initialize a Database Object as simple as:

    // Setting Variables with ARRAY DATA:
    $db_A_Server   = $env['database']['db_A']['server'];
    $db_A_database = $env['database']['db_A']['database'];
    $db_A_username = $env['database']['db_A']['username'];
    $db_A_password = $env['database']['db_A']['password'];

    // Using the function from teh Boilerplate Database Helper (boilerplate.db.php) to connect with the DB via PDO:
    $dsn = buildDSN($db_A_Server, $db_A_database);
    $db_A = connectDB($dsn, $db_A_username, $db_A_password);

The same can be done for the 'db_B' (also present in the db.json sample above), and any other Database you may have
have set, and all can be initialized to be available at the same time in different $db_objects.

You only must do it ONCE!

Then, when you have the DB connected, you can query the Database (as many times as needed) as simple as:

    // Define string with a valid SQL Query
    $Query  = "SELECT * TABLE_NAME WHERE ACTIVE = 1;";

    // Execute the $query against the $db_A using the queryDB() function from the Boilerplate Database Helper file.
    $result = queryDB($db_A, $Query);

If you prepared the SQL Query correctly, the queryDB() function will call the PDO->Prepare to 'prepare' the query and
then will call the PDO->Execute to run it against the Database.

Of course, you  ** MUST ** sanitize all your variables and SQL Queries ** BEFORE ** sending it to the DB, but it is
your job as a developer! The Boilerplate Database Helpers are here only to help and eliminate redundant step.

Besides, you do not need to use it at all. You can use any DB-ORDM, vendor module, or framework of your choice.

But, if you are like us and love to optimize your queries and are tires of how BAD and BLOATED most DB-ORDM, vendor
module, and framework have become, then the queryDB() function will do the job for you and run the Query, returning
the execution result in a flash. By default, it will parse the result into an Array for you, as it is the most common
result PHP applications handle, however, if you need the RAW Database Response Object instead, simply add "true" as
the second (optional) parameter in the queryDB() function call, after the query itself, you will receive the RAW
Database Response Object. Check the manual for more information or inspect the queryDB() function in the
'boilerplate.db.php' file.

After receiving the response, assuming it is NOT false (which denounces a bad query), if no data handling is required,
you can simply send the data to TWIG to be rendered within the template as simple as:

    $twig->addGlobal('total', $result[0]);  // If you want a single resultant item of the Array Object
    $twig->addGlobal('dataset', $result);  // If you want the whole Array to be easily looped by the TWIG Template

That's it!

So, to make it simple:

Connect ONCE:
    $db_A_Server   = $env['database']['db_A']['server'];
    $db_A_database = $env['database']['db_A']['database'];
    $db_A_username = $env['database']['db_A']['username'];
    $db_A_password = $env['database']['db_A']['password'];
    $dsn = buildDSN($db_A_Server, $db_A_database);
    $db_A = connectDB($dsn, $db_A_username, $db_A_password);

Access as many times as needed:
    $Query  = "SELECT * TABLE_NAME WHERE ACTIVE = 1;";
    $result = queryDB($db_A, $Query);
    $twig->addGlobal('dataset', $result);

    $Query  = "SELECT counter(*) AS TOTAL TABLE_NAME WHERE ACTIVE = false;";
    $result = queryDB($db_A, $Query);
    $twig->addGlobal('total', $result[0]);

-->