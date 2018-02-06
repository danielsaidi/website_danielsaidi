---
title:  "Localizing a WPF application with resource files"
date:	2011-05-12 12:00:00 +0100
tags: 	.net wpf l18n
---


I am localizing a WPF application that consists of a main application project as
well as several separate DLL projects that provides the application with general
user controls, model classes etc.

Many of the tutorials I've found suggested using the `App.xaml` file to localize
the application. This does not work for me, since I must be able to localize all
parts of the application, as well as the separate DLLs.

Luckily, it's really easy to get resource file-based localization up and running
for an WPF app. Just follow the steps below and keep in mind that the names that
I use are only boring suggestions. You can go as wild as you want.


## Step 1. Create a WPF application

First, let's create a new WPF application. I call mine... ... ...HelloWorld!

![HelloWorld app](/assets/blog/2011-05-12-1.png)

As you can see, I have also added a button that we are going to localize.


## Step 2. Create the resource file

Now, let’s add a resource file to which we will add textual content. To separate
resource files from the rest of the application, I place the file in a Resources
folder and name it AppLanguage.resx.

![Resource file](/assets/blog/2011-05-12-2.png)

In the image above, you can see a resource file with (so far) a single parameter.

In order to access the resource file from XAML, we have to make the file public:

![Making the resource file public](/assets/blog/2011-05-12-3.png)

Once this is done, let’s proceed by accessing the resource file from XAML.


## Step 3. Access the resource file content from XAML

Now, let’s use the resource file parameter in our XAML file. Connect the context
class to the XAML code by adding the following line into the Window tag:

	xmlns:Resources="clr-namespace:HelloWorld.Resources"

After that, you can access the resource parameter as such:

	<Button Content="{x:Static Resources:AppLanguage.Menu_LoadData_All}"></Button>

Voilá! The text is finally displayed within the button:

![The resource text is displayed within the button](/assets/blog/2011-05-12-4.png)

Since we now use a resource file instead of App.xaml, we can use the same file to
translate textual content code-behind as well.


## Step 4. Access the resource content file from code

To access resource file content from code, simply call the `AppLanguage` class as
such:

	using HelloWorld.Resources; //Add this topmost among the using directives
	MessageBox.Show(AppLanguage.ButtonText);   //Add this code inside the MainWindow() ctor

When we now start our application, the message box is displayed just like we want:

![Message box](/assets/blog/2011-05-12-5.png)