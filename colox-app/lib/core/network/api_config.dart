class ApiConfig {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://127.0.0.1:8000/api', // Chrome/web-এর জন্য localhost সরাসরি কাজ করবে
  );
}