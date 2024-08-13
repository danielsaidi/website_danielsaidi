---
title: Calculate geo distance and bearing in C#
date:  2011-02-04 12:00:00 +0100
tags:  archive
---

I'm building a GPS-based web application that lets mobile devices post their position, then replies with nearby points of interest (POIs) like restaurants, bars, etc.

To achieve this this, the backend must be able to calculate a distance between the device coordinate and the coordinates of each place in the database. 

I couldn't find or code a nice C# implementation, until I stumbled upon [this post](http://myxaab.wordpress.com/2010/09/02/calculate-distance-bearing-between-geolocation/). However, it uses one big class and an enum. Being a [SOLID](https://en.wikipedia.org/wiki/SOLID) kind of this post therefore breaks up the original implementation into interfaces and small classes.


## Step 1 - Break the code up into smaller classes

I first extracted the angle/radian conversion logic into a tiny `AngleConverter` class. Since this logic never changes, it can be a concrete class:

```csharp
class AngleConverter
{
   double ConvertDegreesToRadians(double angle)
   {
      return Math.PI * angle / 180.0;
   }	

   double ConvertRadiansToDegrees(double angle)
   {
      return 180.0 * angle / Math.PI;
   }
}
```

I also created a `DistanceConverter` for converting distances. This can also be a concrete class since the implementation should never change:

```csharp
class DistanceConverter
{
    double ConvertMilesToKilometers(double miles)
    {
       return miles * 1.609344;
    }	

    double ConvertKilometersToMiles(double kilometers)
    {
       return kilometers * 0.621371192;
    }
}
```

Next, I implemented a `DistanceType` enum, to allow for multiple distance types.

```csharp
enum DistanceType
{
    Miles = 0,
    Kilometers = 1
}
```

I then created a `Position` class (could be struct), that has a `Latitude` and a `Longitude`:

```csharp
class Position
{
    Position(double latitude, double longitude)
    {
       Latitude = latitude;
       Longitude = longitude;
    }
	 
    double Latitude { get; set; }
    double Longitude { get; set; }
}
```


## Step 2 - Define interfaces

With these small classes in place, the previously big distance calculator class only contains calculation methods, which now can use a `Position` instead of a latitude/longitude tuple.

Before adjusting it, let's define some interfaces that it should implement to make it possible to switch out any implementation later, if needed:

```csharp
interface IBearingCalculator
{
    double CalculateBearing(Position position1, Position position2);
}

interface IDistanceCalculator
{
    double CalculateDistance(Position position1, Position position2, DistanceType distanceType1);
}

interface IRhumbBearingCalculator
{
    double CalculateRhumbBearing(Position position1, Position position2);
}

interface IRhumbDistanceCalculator
{
    double CalculateRhumbDistance(Position position1, Position position2, DistanceType distanceType);
}
```


## Step 3 - Implement the interfaces

With all this in place, the class can be set to implement the interfaces as such:

```csharp
class PositionHandler : IBearingCalculator, IDistanceCalculator, IRhumbBearingCalculator, IRhumbDistanceCalculator
{
    private readonly AngleConverter angleConverter;

    PositionHandler()
    {
        angleConverter = new AngleConverter();
    }

    static double EarthRadiusInKilometers { get { return 6367.0; } }
    static double EarthRadiusInMiles { get { return 3956.0; } }

    double CalculateBearing(Position position1, Position position2)
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

    double CalculateDistance(Position position1, Position position2, DistanceType distanceType)
    {
        var R = (distanceType == DistanceType.Miles) ? EarthRadiusInMiles : EarthRadiusInKilometers;
        var dLat = angleConverter.ConvertDegreesToRadians(position2.Latitude) - angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var dLon = angleConverter.ConvertDegreesToRadians(position2.Longitude) - angleConverter.ConvertDegreesToRadians(position1.Longitude);
        var a = Math.Sin(dLat / 2) * Math.Sin(dLat / 2) + Math.Cos(angleConverter.ConvertDegreesToRadians(position1.Latitude)) * Math.Cos(angleConverter.ConvertDegreesToRadians(position2.Latitude)) * Math.Sin(dLon / 2) * Math.Sin(dLon / 2);
        var c = 2 * Math.Atan2(Math.Sqrt(a), Math.Sqrt(1 - a));
        var distance = c * R;

        return Math.Round(distance, 2);
    }

    double CalculateRhumbBearing(Position position1, Position position2)
    {
        var lat1 = angleConverter.ConvertDegreesToRadians(position1.Latitude);
        var lat2 = angleConverter.ConvertDegreesToRadians(position2.Latitude);
        var dLon = angleConverter.ConvertDegreesToRadians(position2.Longitude - position1.Longitude);

        var dPhi = Math.Log(Math.Tan(lat2 / 2 + Math.PI / 4) / Math.Tan(lat1 / 2 + Math.PI / 4));
        if (Math.Abs(dLon) &gt; Math.PI) dLon = (dLon &gt; 0) ? -(2 * Math.PI - dLon) : (2 * Math.PI + dLon);
        var brng = Math.Atan2(dLon, dPhi);

        return (angleConverter.ConvertRadiansToDegrees(brng) + 360) % 360;
    }

    double CalculateRhumbDistance(Position position1, Position position2, DistanceType distanceType)
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