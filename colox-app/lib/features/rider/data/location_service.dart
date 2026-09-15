import 'package:geolocator/geolocator.dart';

class LocationService {
  Future<Position> getCurrentPosition() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception('লোকেশন সার্ভিস বন্ধ আছে। অনুগ্রহ করে চালু করুন।');
    }

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        throw Exception('লোকেশন পারমিশন প্রয়োজন।');
      }
    }

    if (permission == LocationPermission.deniedForever) {
      throw Exception('লোকেশন পারমিশন স্থায়ীভাবে বন্ধ। সেটিংস থেকে চালু করুন।');
    }

    return Geolocator.getCurrentPosition(
      locationSettings: const LocationSettings(accuracy: LocationAccuracy.high),
    );
  }
}