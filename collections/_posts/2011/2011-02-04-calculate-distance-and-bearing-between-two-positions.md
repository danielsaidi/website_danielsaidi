---
title: Calculate geo distance and bearing in C#
date:  2011-02-04 12:00:00 +0100
tags:  .net c# geo
---

I'm currently building a gps-based web application that lets mobile devices post
their positions to the app, which then replies with nearby items of interest. To
do this this, the backend has to be able to calculate the distance between two geo
coordinates. Here's how to to do this in C#.

I read up on the topic, but couldn't find or create a nice C# implementation. That
is, until I stumbled upon [this post](http://myxaab.wordpress.com/2010/09/02/calculate-distance-bearing-between-geolocation/). However, it uses one big class
and an enum. Being a [SOLID](https://en.wikipedia.org/wiki/SOLID) kind of guy, I
felt like breaking it up into smaller parts and introduce some of interfaces.


## Step 1 - Break the code up into smaller classes

I first extracted the angle/radian conversion logic into a tiny `AngleConverter`
class. Since this logic never changes, this could be a static class:

```csharp
public class AngleConverter
{
   public double ConvertDegreesToRadians(double angle)
   {
      return Math.PI * angle / 180.0;
   }	

   public double ConvertRadiansToDegrees(double angle)
   {
      return 180.0 * angle / Math.PI;
   }
}
```

I also created a `DistanceConverter` class for converting distances. This could
also be static, since the implementation should never change:

```csharp
public class DistanceConverter
{
    public double ConvertMilesToKilometers(double miles)
    {
       return miles * 1.609344;
    }	

    public double ConvertKilometersToMiles(double kilometers)
    {
       return kilometers * 0.621371192;
    }
}
```

Next, I extracted the `DistanceType` enum into a separate file...

```csharp
public enum DistanceType
{
    Miles = 0,
    Kilometers = 1
}
```

then created a `Position` class (could be struct), that only has a `Latitude`
and a `Longitude` property:

```csharp
public class Position
{
    public Position(double latitude, double longitude)
    {
       Latitude = latitude;
       Longitude = longitude;
    }
	 
    public double Latitude { get; set; }
    public double Longitude { get; set; }
}
```


## Step 2 - Define interfaces

With all these small classes, the previously big class only contains calculation
methods, which now can use `Position` instead of a latitude/longitude tuple.

Before adjusting the class, let's define interfaces that it should implement (to
make it possible to switch out any implementation later, if needed):

```csharp
public interface IBearingCalculator
{
    double CalculateBearing(Position position1, Position position2);
}

public interface IDistanceCalculator
{
    double CalculateDistance(Position position1, Position position2, DistanceType distanceType1);
}

public interface IRhumbBearingCalculator
{
    double CalculateRhumbBearing(Position position1, Position position2);
}

public interface IRhumbDistanceCalculator
{
    double CalculateRhumbDistance(Position position1, Position position2, DistanceType distanceType);
}
```


## Step 3 - Implement the interfaces

With all these small bits and pieces in place, the class can be set to implement
the interfaces as such:

```csharp
public class PositionHandler : IBearingCalculator, IDistanceCalculator, IRhumbBearingCalculator, IRhumbDistanceCalculator
{
    private readonly AngleConverter angleConverter;

    public PositionHandler()
    {
        angleConverter = new AngleConverter();
    }

    public static double EarthRadiusInKilometers { get { return 6367.0; } }
    public static double EarthRadiusInMiles { get { return 3956.0; } }

    public double CalculateBearing(Position position1, Position position2)
    {
        var lat1 = angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var lat2 = angleConverter.ConvertDegreesToRadians(position2.Latitude);
        var long1 = angleConverter.ConvertDegreesToRadians(position2.Longitude);
        var long2 = angleConverter.ConvertDegreesToRadians(position1.Longitude);
        var dLon = long1 - long2;

        var y = Math.Sin(dLon) * Math.Cos(lat2);
        var x = Math.Cos(lat1) * Math.Sin(lat2) - Math.Sin(lat1) * Math.Cos(lat2) * Math.Cos(dLon);
        var brng = Math.Atan2(y, x);

        return (angleConverter.ConvertRadiansToDegrees(brng) + 360) % 360;
    }

    public double CalculateDistance(Position position1, Position position2, DistanceType distanceType)
    {
        var R = (distanceType == DistanceType.Miles) ? EarthRadiusInMiles : EarthRadiusInKilometers;
        var dLat = angleConverter.ConvertDegreesToRadians(position2.Latitude) - angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var dLon = angleConverter.ConvertDegreesToRadians(position2.Longitude) - angleConverter.ConvertDegreesToRadians(position1.Longitude);
        var a = Math.Sin(dLat / 2) * Math.Sin(dLat / 2) + Math.Cos(angleConverter.ConvertDegreesToRadians(position1.Latitude)) * Math.Cos(angleConverter.ConvertDegreesToRadians(position2.Latitude)) * Math.Sin(dLon / 2) * Math.Sin(dLon / 2);
        var c = 2 * Math.Atan2(Math.Sqrt(a), Math.Sqrt(1 - a));
        var distance = c * R;

        return Math.Round(distance, 2);
    }

    public double CalculateRhumbBearing(Position position1, Position position2)
    {
        var lat1 = angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var lat2 = angleConverter.ConvertDegreesToRadians(position2.Latitude);
        var dLon = angleConverter.ConvertDegreesToRadians(position2.Longitude - position1.Longitude);

        var dPhi = Math.Log(Math.Tan(lat2 / 2 + Math.PI / 4) / Math.Tan(lat1 / 2 + Math.PI / 4));
        if (Math.Abs(dLon) &gt; Math.PI) dLon = (dLon &gt; 0) ? -(2 * Math.PI - dLon) : (2 * Math.PI + dLon);
        var brng = Math.Atan2(dLon, dPhi);

        return (angleConverter.ConvertRadiansToDegrees(brng) + 360) % 360;
    }

    public double CalculateRhumbDistance(Position position1, Position position2, DistanceType distanceType)
    {
        var R = (distanceType == DistanceType.Miles) ? EarthRadiusInMiles : EarthRadiusInKilometers;
        var lat1 = angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var lat2 = angleConverter.ConvertDegreesToRadians(position2.Latitude);
        var dLat = angleConverter.ConvertDegreesToRadians(position2.Latitude - position1.Latitude);
        var dLon = angleConverter.ConvertDegreesToRadians(Math.Abs(position2.Longitude - position1.Longitude));

        var dPhi = Math.Log(Math.Tan(lat2 / 2 + Math.PI / 4) / Math.Tan(lat1 / 2 + Math.PI / 4));
        var q = Math.Cos(lat1);
        if (dPhi != 0) q = dLat / dPhi;  // E-W line gives dPhi=0
        // if dLon over 180° take shorter rhumb across 180° meridian:
        if (dLon &gt; Math.PI) dLon = 2 * Math.PI - dLon;
        var dist = Math.Sqrt(dLat * dLat + q * q * dLon * dLon) * R;

        return dist;
    }
}
```

...and that's it! You can now calculate the distance and bearing between two coordinates.

A big thanks to wowi, who posted the original implementation!



