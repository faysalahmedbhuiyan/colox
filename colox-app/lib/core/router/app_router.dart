import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

/// User role — এটাই পরে auth state থেকে আসবে (Phase 1-এ)
enum UserRole { guest, rider, driver, driverInRiderMode }

/// Placeholder auth state — Phase 1-এ real auth দিয়ে replace হবে
class AuthState {
  final UserRole role;
  final bool isLoggedIn;

  const AuthState({required this.role, required this.isLoggedIn});

  static const guest = AuthState(role: UserRole.guest, isLoggedIn: false);
}

/// Route guard — এইখানে business rule enforce হবে:
/// rider-only account কখনো driver route-এ ঢুকতে পারবে না।
String? roleGuard(AuthState authState, String targetPath) {
  if (!authState.isLoggedIn && targetPath != '/login') {
    return '/login';
  }

  final isDriverRoute = targetPath.startsWith('/driver');
  final canAccessDriver =
      authState.role == UserRole.driver ||
      authState.role == UserRole.driverInRiderMode;

  if (isDriverRoute && !canAccessDriver) {
    return '/rider/home';
  }

  return null;
}

final appRouter = GoRouter(
  initialLocation: '/login',
  routes: [
    GoRoute(
      path: '/login',
      builder: (context, state) => const PlaceholderScreen(title: 'Login'),
    ),
    GoRoute(
      path: '/rider/home',
      builder: (context, state) => const PlaceholderScreen(title: 'Rider Home'),
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
      body: Center(child: Text('$title — Phase 1-এ real content আসবে')),
    );
  }
}