<h1><b>Awdd Plugin</b></h1>
Contributors: Awdd<br>
Tags: client, crud, form, security<br>
Requires at least: PHP 5.3.0, ReflectionClass (php native class)<br>
Tested up to: 5.5.12<br>
Stable tag: 0.0.1<br>
License: GPLv2<br>
License URI: http://www.gnu.org/licenses/gpl-2.0.html<br>
 
<hr>
<h2><b>Description</b></h2>
 
<p>This plugin is called AwDD , and is a simple CRUD to customers. 
It allows : add, edit , view and delete a client with basic information.</p>

<p>The plugin uses the latest technology as namespaces , 
Annotation , and is created as a MVC ( Model- view-controller ).</p>

<p>Namespaces represent a significant change in php . It is a more 
orderly method to use classes. Annotations treat comments in a 
class as parameters by ReflectionClass php class.</p>

<p>With the namespaces it used an autoloader (Awdd\__ autoloader.php ), 
responsible for loading all classes and create a map of namespaces.</p>

<p>In this example were not used some key properties of OO . Abstract classes were not created, 
superclasses and inheritance, as it is just simple classes. Then there would be no 
need to implement polymorphism. But both the controller class as the form class might 
be abstract , and create derivations from them through an interface that would force the 
desired methods . However this plugin carries the basics of OO attached to MVC</p>

<p>They have five classes that will be commented one by one:</p>

<h3><b>1 - Module</b></h3>
<p>The Module class, or namespace AwDD is the class invoked by wordpress . </p>
<p>This class is responsible for:</p>
<ul>
	<li>instantiating the menu (wp-admin);</li>
	<li>trace the route based on the address;</li>
	<li>start the database ;</li>
	<li>send the correct action;</li>
</ul>

<h3><b>2 - Controller</b></h3>
<p>The ClientController class or namespace AwDD\Controller\ClientController, 
is responsible for the correct loading of views , according to the request made by 
the user. Thus he takes dispatches the correct action , along with the view required , 
which are required by the Module. He is also responsible for controlling the Db class 
( namespace AwDD\Bd\Bd ), using the functions to select clients, add a new client , 
edit an existing client or a client deleter. The View folder contains templates that 
will be loaded into the controller .</p>

<h3><b>3 - DB</b></h3>
<p>Db class ( namespace AwDD\Bd\Bd ) It is responsible for interacting with the 
database using the wordpress resources. When built , it requires a model ( described 
later on ) which it provides information on the database . The first task is to test 
if the table (described in Client model) exists, and failing that creates it.</p>

<p>She reads the information contained in the form of comments and interprets some 
information (those preceded by @ ) to capture database information. The first annotations 
are above the Model. They store information on the table name . The other information 
is up properties, and interpret these as table columns and one of those is a primarykey.</p>

<p>The concept of annotation has not been fully developed . He just plays some types of 
table columns of information (int and varchar ) . In order to be used needs to implement 
all other types , and each type as a separate class . However, it was used that way to 
demonstrate the advantages of this technology.</p>

<p>He has some public methods that are: find ( to find a through part of the id) , findBy 
(meets all elements with these parameters) , findAll (meets all elements) , persist 
( updates or creates a new record ) , remove ( delete an entry in the table).</p>

<h3><b>4 - Model</b></h3>
<p>The Client Model ( namespace Awdd\Model\Client ) controls the customer 
class with the database. The properties are registered in Client table columns. In 
addition to properties and information ( all through annotations ) , are in this class. 
This is where we define what are the columns, if they can be null or not, the type of data 
and the size of the stored information.</p>

<p>Each piece of information requires a setter and getter . the format is as follows : 
for the foo property ( setFoo and getFoo ).</p>

<h3><b>5 - Form</b></h3>
<p>The ClientForm class ( namespace AwDD\Form\ClientForm ) is responsible for 
generating a secure form. She has some important characteristics :</p>
<ul>
	<li>It generates the input , the label and the error messages , and automatically launches 
	the form ( after being called);</li>
	<li>It hydrates the form with the information posted or uploaded model;</li>
	<li>Allows data validation ( were placed few validations ), and returns error message;</li>
	<li>It includes filters to prevent inadequate information on bd. It removes white spaces 
	at the beginning and end of the string , remove the html tags to avoid injections, and 
	requires the id is of type int;</li>
	<li>It hydrates the Model automatically, form information , seeking the appropriate setter 
	them ( if the input has the e-mail name , he seeks setEmail and puts the input value 
	as a property of this function );</li>
	<li>It serves as validator. Only if the form is validated , the actions in the database 
	will be performed.</li>
</ul>	
<p>Not all inputs have been created for the form. The ideal would be to create a class for each 
input in the case interpret the input text within a function.</p>

<hr>
<h2><b>Installation</b></h2>
 
<p>1. Send the plugin to the folder `/wp-content/plugins/`</p>
<p>2. Activate the administrative area of WordPress.</p>

<hr>
<h2><b>Changelog</b></h2>
 
= 0.0.1 =
<p>* Plugin development.</p>
 
