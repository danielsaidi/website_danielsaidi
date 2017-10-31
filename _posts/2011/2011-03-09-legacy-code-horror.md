---
title:  "Legacy code horror"
date:    2011-03-09 12:00:00 +0100
tags: 	.net
---


When working in a legacy code base of someone else's making, imagine to refactor
code that is mainly built up on structures like this:

	switch (state)
	{
	   case ApplicationStateTimeStampType.Imported:
	      if (p == false)
	      {
	         if (!applicationLoading)
	            tblstate.importStartedTimeStamp = DateTime.Now;
	      }
	      else
	      {
	         if (!applicationLoading)
	            tblstate.importedTimeStamp = DateTime.Now;
	      }
	      break;
	 
	   case ApplicationStateTimeStampType.Validated:
	      if (p == false)
	      {
	         if (!applicationLoading)
	            tblstate.validateStartedTimeStamp = DateTime.Now;
	      }
	      else
	      {
	         if (!applicationLoading)
	            tblstate.validatedTimeStamp = DateTime.Now;
	      }
	      break;
	 
	   case ApplicationStateTimeStampType.Transformed:
	      if (p == false)
	      {
	         if (!applicationLoading)
	            tblstate.transformStartedTimeStamp = DateTime.Now;
	      }
	      else
	      {
	         if (!applicationLoading)
	            tblstate.transformedTimeStamp = DateTime.Now;
	      }
	      break;
	}
	 
	tblstate.changedTimeStamp = DateTime.Now;

Then, consider that the function above is called from a function that begins with
setting `loading` to true, which means that only the last line gets executed.

Ok, that's all I've got.