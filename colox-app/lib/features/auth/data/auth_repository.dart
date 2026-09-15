import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class AuthRepository {
  final Dio _dio = ApiClient.instance;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await _dio.post('/auth/login', data: {
      'email': email,
      'password': password,
    });
    return response.data;
  }

  Future<Map<String, dynamic>> registerRider({
    required String name,
    required String email,
    required String phone,
    required String password,
  }) async {
    final response = await _dio.post('/auth/register/rider', data: {
      'name': name,
      'email': email,
      'phone': phone,
      'password': password,
    });
    return response.data;
  }
}