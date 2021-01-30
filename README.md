## ©Stephan Banse

# Usage
To use the Framework you need PHP Version7 or later installed.

## Development

### Starting the developmentserver

To start the developmentserver execute the dev.php script in the main directory using php.

For this write into the terminal:

`php dev.php server`

If you want to specify the ip / port use the flags `--host=`/`--port=`

**Examples**
`php dev.php server --port=80`
`php dev.php server --host=0.0.0.0 --port=1234`

### **Important**
Don't use the developmentserver for production!

To use the Framework, configure nginx/apache so that the root of the server is the */public/index.php* file.

### Using routes
To show files to the public, simply add a route to that file.
You can do that by editing the file */route/public.php*.

To add a get-Route (A route that listens if the get-method is used), simply add the following:

`$ROUTE->get('YOUR ROUTE HERE', function () {view('YOUR FILE HERE');});`

The view function automatically searches for the files in the */view* directory.

For a post-Route simply change the get to a post.

For routes reffering to controllers enter the routes in the */route/controller.php* file and change the view()-function to process().
Using the process-function you can simply enter a classname and a function like this:

`process('classname::function')`

Only static functions can be called via process().


#### Parameters
To add parameters to your route, write the routes like this:

`/aRouteWith/[Parameter1]/And/[Parameter2]`.
Now to use these parameters, you can use `$Request->parametername`

**Example**

`$ROUTE->get('/posts/[PostId]/', function () {process("Posts::get()");});`

Inside the Postscontroller:

`
public static function get($Request) {
    $postId = $Request->PostId;
}
`

### SQL-Config
To configure the database-connection edit the config.json-file in the main-directory.

You can choose between a mysql-databaseserver and an sqlite3-file.

### Models
A Model is an instance of a Databasetable.

To create a model you can use the dev.php-file.

`php dev.php make model YOUR_MODELNAME_HERE`

After creating the model, enter the file named like the model created in `/app/sql/models/`.
In row 5 edit the value of the variable `$tablename` to the tablename of the table represented by the model.

And that's how you create a model.

If you want to use a model, you need a controller.

### Controllers
To create a controller, simply use the dev.php-file again.

`php dev.php make controller YOUR_CONTROLLERNAME_HERE`

You can also add the `--require=` to import models into your controller. You can import as many models as you want, as long as you use existing models.

Inside the controller you can add static function that process requests and use database models.
