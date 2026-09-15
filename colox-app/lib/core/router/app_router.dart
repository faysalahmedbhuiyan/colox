import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../features/shared/presentation/splash_screen.dart';
import '../../features/auth/presentation/login_screen.dart';
import '../../features/auth/presentation/register_screen.dart';
import '../../features/rider/presentation/rider_home_screen.dart';

enum UserRole { guest, rider, driver, driverInRiderMode }

class AuthState {
  final UserRole role;
  final bool isLoggedIn;
  const AuthState({required this.role, required this.isLoggedIn});
  static const guest = AuthState(role: UserRole.guest, isLoggedIn: false);
}

final appRouter = GoRouter(
  initialLocation: '/splash',
  routes: [
    GoRoute(path: '/splash', builder: (context, state) => const SplashScreen()),
    GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
    GoRoute(path: '/register', builder: (context, state) => const RegisterScreen()),
    GoRoute(
      path: '/rider/home',
      builder: (context, state) => const RiderHomeScreen(),
    ),
    GoRoute(
      path: '/driver/dashboard',
      builder: (context, state) => const PlaceholderScreen(title: 'Driver Dashboard'),
    ),
  ],
);

class PlaceholderScreen extends StatelessWidget {
  final String title;
  const PlaceholderScreen({super.key, required this.title});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: Center(child: Text('$title — পরের ধাপে real content আসবে')),
    );
  }
}