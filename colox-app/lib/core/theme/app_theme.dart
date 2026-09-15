import 'package:flutter/material.dart';

class AppColors {
  static const primaryGreen = Color(0xFF00B050);
  static const darkBlue = Color(0xFF1E293B);
  static const lightGray = Color(0xFFF5F7FA);
  static const accentYellow = Color(0xFFFFC107);
  static const white = Color(0xFFFFFFFF);
  static const error = Color(0xFFE53935);
  static const textMuted = Color(0xFF6B7280);
}

class AppTheme {
  static ThemeData get light {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: AppColors.lightGray,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primaryGreen,
        primary: AppColors.primaryGreen,
        secondary: AppColors.accentYellow,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.white,
        foregroundColor: AppColors.darkBlue,
        elevation: 0,
        centerTitle: false,
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primaryGreen,
          foregroundColor: AppColors.white,
          minimumSize: const Size.fromHeight(52),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          textStyle: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.primaryGreen, width: 1.5),
        ),
      ),
      textTheme: const TextTheme(
        headlineLarge: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: AppColors.darkBlue),
        headlineMedium: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: AppColors.darkBlue),
        bodyLarge: TextStyle(fontSize: 16, color: AppColors.darkBlue),
        bodyMedium: TextStyle(fontSize: 14, color: AppColors.textMuted),
      ),
    );
  }
}