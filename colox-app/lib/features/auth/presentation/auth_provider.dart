import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_riverpod/legacy.dart';
import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';
import '../data/auth_repository.dart';

final authRepositoryProvider = Provider((ref) => AuthRepository());

class AuthController extends StateNotifier<AsyncValue<void>> {
  AuthController(this._repo) : super(const AsyncValue.data(null));
  final AuthRepository _repo;

  Future<bool> login(String email, String password) async {
    state = const AsyncValue.loading();
    try {
      final data = await _repo.login(email, password);
      await ApiClient.saveToken(data['token']);
      state = const AsyncValue.data(null);
      return true;
    } on DioException catch (e) {
      state = AsyncValue.error(
        e.response?.data['message'] ?? 'লগইন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।',
        StackTrace.current,
      );
      return false;
    }
  }

  Future<bool> registerRider(String name, String email, String phone, String password) async {
    state = const AsyncValue.loading();
    try {
      final data = await _repo.registerRider(name: name, email: email, phone: phone, password: password);
      await ApiClient.saveToken(data['token']);
      state = const AsyncValue.data(null);
      return true;
    } on DioException catch (e) {
      final errors = e.response?.data['errors'];
      final message = errors != null
          ? errors.values.first[0]
          : (e.response?.data['message'] ?? 'রেজিস্ট্রেশন ব্যর্থ হয়েছে।');
      state = AsyncValue.error(message, StackTrace.current);
      return false;
    }
  }
}

final authControllerProvider = StateNotifierProvider<AuthController, AsyncValue<void>>((ref) {
  return AuthController(ref.watch(authRepositoryProvider));
});