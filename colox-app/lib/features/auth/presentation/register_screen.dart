import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import 'auth_provider.dart';

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
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
      appBar: AppBar(),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('অ্যাকাউন্ট তৈরি করুন', style: Theme.of(context).textTheme.headlineLarge),
              const SizedBox(height: 4),
              Text('Rider হিসেবে যাত্রা শুরু করুন', style: Theme.of(context).textTheme.bodyMedium),
              const SizedBox(height: 28),
              TextField(controller: _nameController, decoration: const InputDecoration(labelText: 'পুরো নাম')),
              const SizedBox(height: 16),
              TextField(controller: _emailController, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'ইমেইল')),
              const SizedBox(height: 16),
              TextField(controller: _phoneController, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'ফোন নম্বর')),
              const SizedBox(height: 16),
              TextField(controller: _passwordController, obscureText: true, decoration: const InputDecoration(labelText: 'পাসওয়ার্ড')),
              const SizedBox(height: 28),
              ElevatedButton(
                onPressed: isLoading
                    ? null
                    : () async {
                        final success = await ref.read(authControllerProvider.notifier).registerRider(
                              _nameController.text.trim(),
                              _emailController.text.trim(),
                              _phoneController.text.trim(),
                              _passwordController.text,
                            );
                        if (success && context.mounted) context.go('/rider/home');
                      },
                child: isLoading
                    ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                    : const Text('রেজিস্টার করুন'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}