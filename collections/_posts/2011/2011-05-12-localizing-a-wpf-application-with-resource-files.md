---
title: Localizing a WPF application with resource files
date:  2011-05-12 12:00:00 +0100
tags:  .net localization
icon:  dotnet
---

This post will show you how to localize a WPF application that consists of a 
main application as well as several separate DLL projects that provides it with
general user controls, model classes etc.

Many tutorials suggests using the `App.xaml` file to localize WPF applications.
This doesn't work for me, since I must be able to localize the application, as
well as separate DLLs.

Luckily, it's really easy to enable resource file-based localization for an WPF
app. Just follow the steps below. Keep in mind that the names I use are just 
suggestions. You can use any names you want.


## Step 1. Create a WPF application

First, let's create a new WPF application. I call mine... ... ...HelloWorld!

![HelloWorld app](/assets/blog/2011/2011-05-12-1.png)

I have added a button that we are going to localize.


## Step 2. Create the resource file

Let’s add a resource file to which we will add textual content. To separate
resource files from the rest of the application, I place it in a `Resources`
folder and calle it `AppLanguage.resx`.

![Resource file](/assets/blog/2011/2011-05-12-2.png)

In order to access the resource file from XAML, we have to make the file public:

![Making the resource file public](/assets/blog/2011/2011-05-12-3.png)

Once this is done, let’s proceed by accessing the resource file from XAML.


## Step 3. Access the resource file content from XAML

To access the resource file in XAML, connect the context class to the XAML code
by adding the following line into the Window tag:

	xmlns:Resources="clr-namespace:HelloWorld.Resources"

After that, you can access the resource parameter as such:

	<Button Content="{x:Static Resources:AppLanguage.Menu_LoadData_All}"></Button>

Voilá! The text is now displayed within the button:

![The resource text is displayed within the button](/assets/blog/2011/2011-05-12-4.png)

Since we now use a resource file instead of App.xaml, we can use the same file to
translate content in the code-behind as well.


## Step 4. Access the resource content file from code

To access resource file content from code, simply call the `AppLanguage` class as
such:

	using HelloWorld.Resources; //Add this topmost among the using directives
	MessageBox.Show(AppLanguage.ButtonText);   //Add this code inside the MainWindow() ctor

When we now start our application, the message box is displayed just like we want:

![Message box](/assets/blog/2011/2011-05-12-5.png)