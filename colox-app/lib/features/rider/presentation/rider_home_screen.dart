import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../../../core/theme/app_theme.dart';
import '../data/location_service.dart';

class RiderHomeScreen extends StatefulWidget {
  const RiderHomeScreen({super.key});

  @override
  State<RiderHomeScreen> createState() => _RiderHomeScreenState();
}

class _RiderHomeScreenState extends State<RiderHomeScreen> {
  final _locationService = LocationService();
  final _mapController = MapController();

  LatLng? _currentLatLng;
  bool _isLoadingLocation = true;
  String? _locationError;

  static const _fallbackCenter = LatLng(23.8103, 90.4125); // Dhaka, লোকেশন না পেলে fallback

  @override
  void initState() {
    super.initState();
    _loadCurrentLocation();
  }

  Future<void> _loadCurrentLocation() async {
    setState(() {
      _isLoadingLocation = true;
      _locationError = null;
    });

    try {
      final position = await _locationService.getCurrentPosition();
      final latLng = LatLng(position.latitude, position.longitude);
      setState(() {
        _currentLatLng = latLng;
        _isLoadingLocation = false;
      });
      _mapController.move(latLng, 16);
    } catch (e) {
      setState(() {
        _locationError = e.toString().replaceFirst('Exception: ', '');
        _isLoadingLocation = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          FlutterMap(
            mapController: _mapController,
            options: MapOptions(
              initialCenter: _currentLatLng ?? _fallbackCenter,
              initialZoom: 14,
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.colox.colox_app',
              ),
              if (_currentLatLng != null)
                MarkerLayer(markers: [
                  Marker(
                    point: _currentLatLng!,
                    width: 40,
                    height: 40,
                    child: const Icon(Icons.my_location, color: AppColors.primaryGreen, size: 32),
                  ),
                ]),
            ],
          ),

          if (_isLoadingLocation)
            const Positioned(
              top: 100,
              left: 0,
              right: 0,
              child: Center(child: CircularProgressIndicator(color: AppColors.primaryGreen)),
            ),

          if (_locationError != null)
            Positioned(
              top: 90,
              left: 16,
              right: 16,
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppColors.error.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Row(
                  children: [
                    Expanded(child: Text(_locationError!, style: const TextStyle(color: AppColors.error, fontSize: 13))),
                    TextButton(onPressed: _loadCurrentLocation, child: const Text('আবার চেষ্টা করুন')),
                  ],
                ),
              ),
            ),

          Positioned(
            right: 16,
            bottom: 210,
            child: FloatingActionButton(
              heroTag: 'my-location-btn',
              backgroundColor: AppColors.white,
              foregroundColor: AppColors.darkBlue,
              onPressed: _loadCurrentLocation,
              child: const Icon(Icons.my_location),
            ),
          ),

          Align(
            alignment: Alignment.bottomCenter,
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 28),
              decoration: const BoxDecoration(
                color: AppColors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('কোথায় যাবেন?', style: Theme.of(context).textTheme.headlineMedium),
                  const SizedBox(height: 16),
                  InkWell(
                    borderRadius: BorderRadius.circular(12),
                    onTap: _currentLatLng == null
                        ? null
                        : () {
                            // পরের ধাপে (Step F3): destination selection screen-এ navigate করবে
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('গন্তব্য নির্বাচন — পরের ধাপে আসবে')),
                            );
                          },
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      decoration: BoxDecoration(
                        color: AppColors.lightGray,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.search, color: AppColors.textMuted),
                          SizedBox(width: 10),
                          Text('গন্তব্য খুঁজুন', style: TextStyle(color: AppColors.textMuted, fontSize: 15)),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}