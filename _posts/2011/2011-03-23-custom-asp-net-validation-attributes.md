---
title:  "Custom ASP.NET validation attributes"
date:   2011-03-23 12:00:00 +0100
tags: 	.net c# web validation
---


After some discussions with my colleagues about my latest focus areas, I felt it
could be nice to write a post about custom validation attributes in ASP.NET. It's
something I use to great extent.

For instance, consider these validation scenarios:

- E-mail addresses
- Postal codes
- Social security numbers
- URLs

All the examples above could to some extent be expressed as regular expressions,
although some may require futher validation than string format. Let's start off
by looking at four implementations (Swedish context):

	public class EmailAddressAttribute : RegularExpressionAttribute
	{
	   public EmailAddressAttribute()
	      : base(@"^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$") { }	

	   public override bool IsValid(object value)
	   {
	      if (value == null || value.ToString().IsNullOrEmpty())
	         return false;
	      return base.IsValid(value);
	   }
	}	

	public class SwedishPostalCodeAttribute : RegularExpressionAttribute
	{
	   public SwedishPostalCodeAttribute(bool optionalSpace = false)
	      : base(optionalSpace ? "^\\d{3}\\ ?\\d{2}$" : "^\\d{5}$") { }	

	   public override bool IsValid(object value)
	   {
	      if (value == null || value.ToString().IsNullOrEmpty())
	         return false;
	      return base.IsValid(value);
	   }
	}	

	public class SwedishSsnAttribute : RegularExpressionAttribute
	{
	   public SwedishSsnAttribute()
	      : base("^\\d{6}-?\\d{4}$") { }	

	   public override bool IsValid(object value)
	   {
	      if (value == null || value.ToString().IsNullOrEmpty())
	         return false;	

	      //Remove possible dash
	      var noDash = value.ToString().Replace("-", "");	

	      //Verify the Luhn algorithm
	      var sum = 0;
	      for (var i = 0; i &lt; 9; i++)
	      {
	         var tmpInt = int.Parse(noDash[i].ToString());
	         tmpInt = tmpInt * (((i + 1) % 2) + 1);
	         sum += (tmpInt &gt; 9) ? tmpInt - 9 : tmpInt;
	         sum = (sum &gt; 10) ? sum - 10 : sum;
	      }	

	      //Verify the check digit
	      return (10 - sum) == int.Parse(noDash[9].ToString());
	   }
	}	

	public class UrlAttribute : RegularExpressionAttribute
	{
	   public UrlAttribute()
	      : base(@"^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;amp;:/~\+#]*[\w\-\@?^=%&amp;amp;/~\+#])?") { }	

	   public override bool IsValid(object value)
	   {
	      if (value == null || value.ToString().IsNullOrEmpty())
	         return false;
	      return base.IsValid(value);
	   }
	}

As you can see, the validation attributes for e-mail, postal codes and urls only
use a regular expression as well as a null/empty condition. The social security
number validation attribute, however, does a bit more.

What is great with this two step approach, is that the regex step can be made to
automatically trigger in JavaScript. The `RegularExpressionAttribute` attribute
handles this automatically, if you inject the correct JavaScripts client-side. A
second validation will then take place when the user posts a form with an ssn.

However, the Swedish postal code validation attribute is insufficient. It should
also verify (with some external service perhaps?) that the postal code actually
exists, but hey...that requires some out of scope integrations :)

This gathers the regex and further validation in one place. If one would need to
use the regex separately, it can be accessed with the `Pattern` property. 

