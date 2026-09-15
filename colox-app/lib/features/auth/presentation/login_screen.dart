import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import 'auth_provider.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);
    final isLoading = authState.isLoading;

    ref.listen(authControllerProvider, (previous, next) {
      next.whenOrNull(
        error: (error, _) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(error.toString()), backgroundColor: AppColors.error),
          );
        },
      );
    });

    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 60),
              Container(
                width: 64, height: 64,
                decoration: BoxDecoration(
                  color: AppColors.primaryGreen.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: const Icon(Icons.alt_route_rounded, color: AppColors.primaryGreen, size: 32),
              ),
              const SizedBox(height: 24),
              Text('স্বাগতম', style: Theme.of(context).textTheme.headlineLarge),
              const SizedBox(height: 4),
              Text('আপনার অ্যাকাউন্টে লগইন করুন', style: Theme.of(context).textTheme.bodyMedium),
              const SizedBox(height: 32),
              TextField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: const InputDecoration(labelText: 'ইমেইল', hintText: 'you@example.com'),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _passwordController,
                obscureText: _obscurePassword,
                decoration: InputDecoration(
                  labelText: 'পাসওয়ার্ড',
                  suffixIcon: IconButton(
                    icon: Icon(_obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined),
                    onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                  ),
                ),
              ),
              const SizedBox(height: 28),
              ElevatedButton(
                onPressed: isLoading
                    ? null
                    : () async {
                        final success = await ref.read(authControllerProvider.notifier).login(
                              _emailController.text.trim(),
                              _passwordController.text,
                            );
                        if (success && context.mounted) context.go('/rider/home');
                      },
                child: isLoading
                    ? const SizedBox(
                        width: 22, height: 22,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                      )
                    : const Text('লগইন করুন'),
              ),
              const SizedBox(height: 20),
              Center(
                child: TextButton(
                  onPressed: () => context.push('/register'),
                  child: const Text('নতুন অ্যাকাউন্ট খুলুন'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}